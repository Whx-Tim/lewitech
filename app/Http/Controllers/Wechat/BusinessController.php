<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Requests\Business\StoreRequest;
use App\Models\Business;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Qiniu\Auth as QiniuAuth;

class BusinessController extends Controller
{
    public function getData()
    {
        $data = file_get_contents('httP://weijuan.szu.edu.cn/n/api/v1/index/map/business');
        $data = json_decode($data)->data;

        foreach ($data as $datum) {
            $content = [];
            foreach ($datum as $key => $item) {
                $content[str_replace('business_', '', $key)] = $item;
                $content['user_id'] = 154;
            }
            unset($content['id']);

            if (is_null($content['branch_address'])) {
                unset($content['branch_address']);
            } else {
                $branches = json_decode($content['branch_address']);
                unset($content['branch_address']);
                $business = Business::create($content);
                foreach ($branches as $branch) {
                    $business->branches()->create([
                        'name' => $branch->business_store,
                        'address' => $branch->business_address,
                        'phone' => $branch->business_phone
                    ]);
                }
            }
        }

        dd('successful');


    }

    public function index()
    {
        return view('wechat.business.index');
    }

    public function getIndex(Request $request)
    {
        $data=$request->all();
        $business = Business::where('status',1);
        // if (isset($data['sw_lat'])){
        //     $business=$business
        //     ->where('business_lat','>=',$data['sw_lat'])
        //     ->where('business_lat','<=',$data['ne_lat'])
        //     ->where('business_lng','>=',$data['sw_lng'])
        //     ->where('business_lng','<=',$data['ne_lng']);
        // }
        $business=$business->get();

        $data_back=[
            "errcode"=> 0,
            "errmsg"=>  '操作成功',
            "data"=>  $business->toarray()
        ];
        return response()->json($data_back);
    }

    public function add(Request $request)
    {
        $lat = $request->get('lat');
        $lng = $request->get('lng');
        $token = $this->getQiniuToken();

        return view('wechat.business.add', compact('lat', 'lng', 'token'));
    }

    protected function getQiniuToken()
    {
        $auth = new QiniuAuth('d2imjQtPHDgS_pA6ILYLHfZaXrpUz-pj6kCIV49Y', 'nphKPWgKwgcejmkgNqYcui1uIGZwkfb7ZYIl667S');
        $policy = array(
            'saveKey'=>'image/business/$(year)$(mon)$(day)_$(hour)$(min)$(sec)_'. str_random(10) .'$(fname)',
        );
        $bucket='weijuan';
        $upToken = $auth->uploadToken($bucket, null, 3600, $policy);

        return $upToken;
    }


    public function edit(Business $business)
    {
        return view('wechat.business.edit', compact('business'));
    }

    public function comment(Business $business)
    {
        if (request()->ajax()) {
            if ($business->comments()->where('user_id', Auth::id())->first()) {
                return $this->ajaxReturn(1, '您已经评论过了，请不要重复评论');
            } else {
                return $this->ajaxReturn(0, '操作成功', ['redirect' => route('wechat.business.comment', ['business' => $business->id])]);
            }
        }
        $poster = $this->compactQiniuUrl($business->poster, 600);

        return view('wechat.business.comment', compact('business', 'poster'));
    }

    public function detail(Business $business)
    {
        $imageWith200 = [];
        $imageWith600 = [];
        foreach (explode(',',$business->image) as $key=>$item) {
            if ($key) {
                array_push($imageWith200,$this->compactQiniuUrl($item,200));
                array_push($imageWith600,$this->compactQiniuUrl($item,600));
            }
        }
        $posterWith600 = $this->compactQiniuUrl($business->poster, 600);
        $comments = $business->comments;
        $commentInfo = [];
        foreach ($comments as $comment) {
            array_push($commentInfo, [
                'nickname' => $comment->user->detail->nickname,
                'head_img' => $comment->user->detail->head_img,
                'content'  => $comment->content,
                'score'    => json_decode($comment->data)->score
            ]);
        }

        $js = app('wechat')->js;
        return view('wechat.business.detail', compact('js', 'business', 'commentInfo', 'imageWith200', 'imageWith600', 'posterWith600'));
    }

    protected function compactQiniuUrl($image, $width)
    {
        $url = "http://wj.qn.h-hy.com/{$image}?imageMogr/auto-orient/thumbnail/{$width}x";

        return $url;
    }

    public function discount_detail(Business $business)
    {
        $poster = $this->compactQiniuUrl($business->poster, 600);
        return view('wechat.business.discount_detail', compact('business', 'poster'));
    }

    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        if ($user->adminset < 3 ) {
            $user->update(['adminset' => '3']);
        }
        $data = $request->except(['_token']);
        $data['share'] = $data['name'].'正式入驻深圳大学校企地图，欢迎深大人支持我的事业';
        $user->businesses()->create($data);

        return $this->ajaxReturn(0, '您的入驻申请已成功提交，我们的工作人员将会联系您进行审核，请耐心等待。', ['redirect' => route('wechat.business.index')]);
    }

    public function update(StoreRequest $request, Business $business)
    {
        if ($business->status === 0) {
            return ['status' => 0,'message' => '您的入驻申请正在审核当中,请等待工作人员与您联系.'];
        } elseif ($business->status === 1) {
            $business->update($request->except(['_token']));

            return ['status' => 1,'message' => '编辑成功', 'redirect' => url('map/business/detail/'.$business->id)];
        } else {
            return ['status' => 2, 'message' => '您的入驻信息已经被关闭了'];
        }
    }
    
    public function storeComment(Request $request, Business $business)
    {
        $this->validate($request, [
            'score' => 'required|max:5|min:1',
            'money' => 'required|min:1',
            'content'  => 'required'
        ],[
            'content.required'  => '请填写评论内容.',
            'money.required' => '请输入消费的金额.',
            'score.required' => '请输入您的评分.',
        ]);
        //评论控制
        if ($business->comments()->where('user_id', Auth::id())->first()) {
            return $this->ajaxReturn(1, '您已经评论过了，请不要重复评论', ['redirect' => route('wechat.business.detail', ['business' => $business->id])]);
        }
        $comment['content'] = $request->input('content');
        $data = $request->only(['score', 'money']);
        $comment['data'] = json_encode($data);
        $comment['user_id'] = Auth::id();

        //插入评论信息
        $business->comments()->create($comment);

        $comments = $business->comments;
        $score = 0;
        $money = 0;
        $count = count($comments);
        foreach ($comments as $comment) {
            $data = json_decode($comment);
            $score += $data->score;
            $money += $data->money;
        }
        $score = $score/$count;
        $money = $money/$count;
        $business->score = $score;
        $business->price = $money;
        $business->save();

        $this->ajaxReturn(0, '评论成功', ['redirect' => route('wechat.business.detail', ['business' => $business->id])]);
    }

}
