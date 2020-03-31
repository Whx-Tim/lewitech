<div class="page page-file active">
    <div class="navbar">
        <span class="again-btn">重做</span>
        <span class="pull-right next-btn">下一步</span>
    </div>
    <p class="guide-text">
        此时此刻你在哪里？<br>
        请拍一张照片，跟朋友分享
    </p>

    <form id="file-form">
        <div class="file-upload-box">
            <img src="{{ asset('images/palette/camera.png') }}" alt="">
            <p>来一张你最喜欢的照片作场景吧~</p>
        </div>
        <input type="file" name="file" id="file" accept="image/*">
    </form>
</div>