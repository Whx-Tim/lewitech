
var display_result = $$('display_result')[0];

$$('buttom_submit')[0].addEventListener('click',function(){
    $('avatar_upload').click();
});

var changeImg = $$('changeImg')[0];

function headwearContainer(){
    var len = 12;
    var ul = document.createElement('ul');
    // ul.style.width = len * 44 + 'px';
    changeImg.appendChild(ul);

    //定时加载一批
    var step = 10;//每次加载10个
    var start = 0;
    function loadImg(){
        for(var i = start; i <= start + step && i < len; i++){
            !function(){
                var li = document.createElement('li');
                var img = document.createElement('img');
                img.setAttribute('src','/n/assets/index/resume/img/'+i+'.png');
                li.onclick = function(){
                    avatar_editor.changeImg(img);
                }
                li.appendChild(img);
                ul.appendChild(li);
            }();
        }
        start = i;
        if (start<=len)
            setTimeout(loadImg,800);
    }
    loadImg();
    this.addRandom = function (){
        var rand = Math.random();
        var rand = Math.ceil(rand*47);
        ul.getElementsByTagName('li')[rand].onclick();
    }
}

var headwear = new headwearContainer();

function img_edit(canvas,img){
    var c_x = canvas.width;
    var c_y = canvas.height;
    var img_x = img.width;
    var img_y = img.height;
    var x = 0;
    var y = 0;
    var d = 0;
    var z = 5;
    this.show = function (){
        var context = canvas.getContext('2d');
        context.clearRect(0,0,c_x,c_y);
        context.save();
        context.translate(x + c_x/2,y + c_y/2);
        context.rotate(degree(d));
        context.drawImage(img,-img_x*z/2,-img_y*z/2,img_x*z,img_y*z);
        context.restore();
    }
    this.moveX = function(len){
        x += len;
        this.show();
    }
    this.moveY = function(len){
        y += len;
        this.show();
    }
    this.zoom = function(len){
        z = len;
        this.show();
    }
    this.rotate = function(len){
        d = len;
        this.show();
    }
    this.changeImg = function(replace_img){
        if(replace_img.complete){
            img = replace_img;
            updateInfo();
            this.show();
        }else{
            alert('加载图片失败~');
        }
    }
    this.toImg = function(){
        return canvas.toDataURL();
    }
    function updateInfo(){
        c_x = canvas.width;
        c_y = canvas.height;
        img_x = img.width;
        img_y = img.height;
        // x = 0;
        // y = 0;
        // d = 0;
        // z = 2.5;
    }
    function degree(n){
        return n * Math.PI / 180;
    }
}

var avatar_editor;

var timer = setInterval(function() {
    if (changeImg.getElementsByTagName('img')[0].complete) {
        avatar_editor = new img_edit($('avatar_edit'),changeImg.getElementsByTagName('img')[0]);
        avatar_editor.show()
        clearInterval(timer);
    }
}, 100);

$('avatar_upload').onchange = function(){
    if(this.files.length == 1){
        var reader = new FileReader();
        reader.onloadend = function(){
            $('avatar_orignal').setAttribute('src',reader.result);
        }
        reader.readAsDataURL(this.files[0]);
    }
}

function generate_avatar(){
    var ajusted_img = new Image();
    ajusted_img.onload=function(){alert("img is loaded");alert(ajusted_img.complete)}; 
    var aaa = avatar_editor.toImg();
    ajusted_img.src = aaa;
    alert(aaa);
    // var start = new Date();
    // while(new Date() - start < 100) { // delay 1 sec
    //     ;
    // }
    alert(ajusted_img.complete);
    if(ajusted_img.complete){
    alert('generate_avatar3');
        var canvas = document.createElement('canvas');
        canvas.width = 600;
        canvas.height = 600;
        canvas.style.width = '200px';
        canvas.style.height = '200px';
        var context = canvas.getContext('2d');
        context.drawImage($('avatar_orignal'),0,0,600,600);
        context.drawImage(ajusted_img,0,0,600,600);
    }
    return canvas.toDataURL();
}

function generate_avatar_callback(callback){
    var ajusted_img = new Image();
    ajusted_img.onload=function(){

        var canvas = document.createElement('canvas');
        canvas.width = 2480;
        canvas.height = 3508;
        canvas.style.width = '2480px';
        canvas.style.height = '3508px';
        var context = canvas.getContext('2d');
        context.drawImage($('avatar_orignal'),0,0,2480,3508);
        context.drawImage(ajusted_img,0,0,2480,3508);
        callback(canvas.toDataURL());
    }; 
    ajusted_img.src = avatar_editor.toImg();
}
var link;
function show_new_avatar(token){
    generate_avatar_callback(function(data){
        document.getElementById('send-btn').isDisabled = true;
       // $('output_img').setAttribute('src', data);
        var xhr = new XMLHttpRequest();
        var url = 'http://upload-z2.qiniu.com/putb64/-1';
        // var code = document.getElementById('output').childNodes[1].src;
        var code = data;
        code = String(code);
        code = code.replace('data:image/png;base64,', '');
        // console.log(code.replace('data:image/png;base64,', ''));
        xhr.open('POST', url, true);
        xhr.setRequestHeader("Content-Type", "application/octet-stream");
        xhr.setRequestHeader("Authorization", "UpToken "+token);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    alert('生成成功，请长按保存您的简历~');
                    document.getElementById('output').childNodes[1].src = 'http://wj.qn.h-hy.com/'+JSON.parse(xhr.responseText).key;
                    link = JSON.parse(xhr.responseText).key;
                    document.getElementById('long-save').style.display = 'block';
                    $('send-btn').style.backgroundColor = 'rgb(38,174,40)';
                    $('send-btn').isDisabled = false;
                } else {
                    alert('生成失败，请稍后重试');
                    console.log(xhr.responseText);
                }

            }
        };
        // xhr.upload.onprogress = function(evt){
        //     //侦查附件上传情况
        //     //通过事件对象侦查
        //     //该匿名函数表达式大概0.05-0.1秒执行一次
        //     //console.log(evt);
        //     //console.log(evt.loaded);  //已经上传大小情况
        //     //evt.total; 附件总大小
        //     var loaded = evt.loaded;
        //     var tot = evt.total;
        //     var per = Math.floor(100*loaded/tot);  //已经上传的百分比
        //     var son =  document.getElementById('output_img');
        //     son.alt = '简历正在生成......'+per+"%";
        //     // son.style.width=per+"%";
        // };
        xhr.send(code);
    });
    document.getElementById('mask').style.zIndex = "100";
    document.getElementById('mask').style.display = "block";
    document.getElementById('mask').style.opacity = "0.3";
    document.getElementById('output').style.zIndex = "100";
    document.getElementById('output').style.display = "block";
    document.getElementById('output').style.opacity = "1";
    // alert(document.getElementById('output_img').src);
    maskOn(function(){
        $('output_img').src = '';
        // $('send-btn').addEventListener('touchstart',function() {
        //     document.getElementById('send-btn').addEventListener('touchstart', sendEmail(), false);
        // });
        $('send-btn').style.backgroundColor = 'rgb(38,174,40)';
        $('send-btn').isDisabled = false;
        // document.getElementById('output_img').alt='简历正在生成.....';
        css($('output'),{
                'opacity': '0'
            });
        setTimeout(function() {
            css($('output'),{
                'display': 'none',
                'z-index': '-10',
            });
        }, 100);
    });
    // css($('output'),{
    //     'display': 'block',
    //     'z-index': '100',
    // });
    // setTimeout(function() {
    //     css($('output'),{
    //         'opacity': '1'
    //     });
    // }, 100);
    // ajax('get','action.php',{act:'generate'},'',function(){});
}

var hammertime = new Hammer($('avatar_edit'));
hammertime.get('pinch').set({ enable: true });
hammertime.get('rotate').set({ enable: true });
hammertime.get('pan').set({ direction: Hammer.DIRECTION_ALL });

hammertime.on("pinchstart", function(e) {
    // hammertime.startPinch = hammertime.startPinch || 1.1;
    // hammertime.tempPinch = hammertime.tempPinch || 1.1;
    hammertime.startPinch = hammertime.startPinch || 1;
    hammertime.tempPinch = hammertime.tempPinch || 1;
	hammertime.startPinch = hammertime.startPinch + hammertime.tempPinch;
	hammertime.lastPinch = e.scale;
});

hammertime.on("pinchmove", function(e) {
    hammertime.tempPinch = (e.scale - hammertime.lastPinch)*3;
    avatar_editor.zoom(hammertime.startPinch + hammertime.tempPinch);
});

hammertime.on("rotatestart", function(e) {
    hammertime.startRotate = hammertime.startRotate || 0;
    hammertime.tempRotate = hammertime.tempRotate || 0;
	hammertime.startRotate = hammertime.startRotate + hammertime.tempRotate;
	hammertime.lastRotate = e.rotation;
});

hammertime.on("rotatemove", function(e) {
    hammertime.tempRotate = e.rotation - hammertime.lastRotate;
    avatar_editor.rotate(hammertime.startRotate + hammertime.tempRotate);
});


hammertime.on('pan pinch', function(ev) {
    avatar_editor.moveY(ev.velocityY*31);
    avatar_editor.moveX(ev.velocityX*31);
});


function add_more(){
    alert('请再点击装饰物添加！');
    generate_avatar_callback(function(data){
       $('avatar_orignal').setAttribute('src', data);
    });
    headwear.addRandom();
}