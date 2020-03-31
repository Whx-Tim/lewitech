@extends('layouts.wechat')

@push('head')
    <style>
        html,
        body {
            width: 100%;
        }

        * {
            font-family: "Lantinghei TC", "Microsoft YaHei", sans-serif;
        }

        body {
            background-color: #efefef;
        }

        section.container {
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 50px;
        }

        .general-btn {
            display: block;
            margin-top: 20px;
            background-color: #48acc8;
            color: #fff;
            padding: 15px;
            border: none;
            width: 100%;
            letter-spacing: .4em;
            font-size: 15px;
            font-weight: 400;
        }

        .general-btn[disabled] {
            background-color: rgb(200,200,200);
        }

        .umbrella-form {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            background-color: rgba(255,255,255,0);
        }

        .umbrella-form .form-group{
            display: block;
            margin: 15px 0;
            padding: 10px;
            background-color: #ffffff;
        }
        .umbrella-form .form-group .form-group-icon {
            display: inline-block;
            width: 25px;
            height: 25px;
            vertical-align: text-bottom;
        }
        .umbrella-form .form-group .form-group-input {
            display: inline-block;
            width: calc(100% - 60px);
            padding: 5px;
            font-size: 15px;
            outline: none;
            border: none;
        }

        .umbrella-form .form-group.vcode .form-group-input {
            width: calc(100% - 150px);
        }
        .umbrella-form .form-group.vcode .form-group-button {
            display: inline-block;
            border: none;
            background-color: #6bcc5e;
            color: #fff;
            padding: 10px 15px;
            outline: none;
            width: 100px;
        }

        .umbrella-form .form-submit-btn {
            display: block;
            margin-top: 20px;
            background-color: #48acc8;
            color: #fff;
            padding: 15px;
            border: none;
            width: 100%;
            letter-spacing: .4em;
            font-size: 15px;
            font-weight: bold;
        }

        .top {
            background-color: #5bd0ce;
            position: relative;
        }
        .top h3{
            text-align: center;
            color: #fff;
            padding: 10px;
            letter-spacing: .2em;
            font-weight: 400;
        }
        .top .top-icon {
            width: 10px;
            position: absolute;
            top: 14px;
            left: 15px;
        }
    </style>
@endpush

@push('javascript')
<script>
    $('#return-btn').click(function () {
        history.go(-1);
    });
</script>
@endpush