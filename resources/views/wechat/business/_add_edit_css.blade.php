<style>
    body {
        padding: 0;
        margin: 0;
        font-family: Microsoft YaHei, sans-serif;
    }

    .business-list {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .business-input-item {
        padding-left: 12px;
        min-height: 37px;
        position: relative;
        border-bottom: 1px solid #f0f0f0;
        box-sizing: border-box;
    }
    .business-input-item:last-child {
        /*border-bottom: 1px solid rgb(212,212,212);*/
    }

    .business-input-item .item-tag {
        font-size: 15px;
        color: #909090;
        position: absolute;
        min-height: 37px;
        line-height: 37px;
    }

    .business-input-item .text-input {
        padding-left: 65px;
        padding-right: 12px;
    }

    .business-box-shadow {
        box-shadow: 0 1.5px 3px rgb(200,200,200);
    }

    .business-input-item .text-input textarea,
    .business-input-item .text-input input,
    .business-input-item .text-input select,
    .business-submit-btn .btn-save {
        font-family: Microsoft YaHei, sans-serif;
        font-size: 15px;
        border: none;
        outline: none;
    }

    .business-input-item .text-input textarea {
        resize: none;
        padding: .5em;
    }

    .business-input-item .text-input input {
    }

    .business-input-item .text-input select {
        border: solid 1px #f65490;
        border-radius: 3px;
        margin: 2px 0;
        appearance:none;
        -moz-appearance:none;
        -webkit-appearance:none;
        background: url("/images/business/arrow.png") no-repeat scroll right center transparent;
        background-size: 10pt;
        background-position-x: 96%;
        padding-right: 14px;
    }

    select::-ms-expand{ display: none; }

    .upload-file {
        padding-top: 8px;
        padding-bottom: 8px;
        background-color: #ffffff;
        width: 100%;
        border: none;
    }

    .upload-file .file_upload {
        opacity: 0;
        display: block;
        width: 75px;
        height: 75px;
        position: absolute;
        top: 50px;
        border: 2px solid #acacac;
        box-sizing: border-box;
    }

    .upload-file .img-upload-btn {
        display: block;
        width: 75px;
        height: 75px;
        border: 4px solid #bdbdbd;
        color: #E0E0E0;
        font-size: 80px;
        line-height: 55px;
        box-sizing: border-box;
        text-align: center;
        font-weight: 900;
        text-shadow: -2px 0 4px black;
    }

    #fileForm .upload-file,
    .multiple-upload{
        min-height: 150px;
    }

    #mainForm {
        margin-top: 12pt;
    }

    #poster-upload {
        width: 85px;
    }

    .m-md-top {
        margin-top: 10px;
    }

    .business-input {
        display: block;
        width: 100%;
        padding: .5em;
        box-sizing: border-box;
    }

    .business-text {
        display: block;
        width: 100%;
        box-sizing: border-box;
        overflow: visible;
    }

    .maskLayer {
        z-index: 15;
        position: absolute;
        top: 45px;
        left: 7px;
        height: auto;
        width: 90%;
    }

    .business-submit-btn {
        /*display: flex;*/
        /*justify-content: center;*/
        /*align-items: center;*/
        /*min-height: 65px;*/
        /*width: 100%;*/
        display: block;
        width: calc(100% - 70pt);
        /*height: 28px;*/
        font-size: 18px;
        line-height: 27px;
        text-align: center;
        color: #FFFFFF;
        padding: 13px 0;
        background-color: #e24f81;
        border-radius: 30px;
        letter-spacing: 3px;
        margin: 20px auto;
        margin-top: 30px;
    }

    .business-submit-btn .btn-save {
        text-decoration: none;
        color: #fff;
        height: 25px;
        width: 200px;
        background-color: #e24f81;
        border-radius: 15px;
        margin: 30pt 0;
        padding: .5em;
        text-align: center;
    }
</style>