<?php
return [
    'Upload' => [
        //最大上传图片大小 单位:M
        'UserImageSize' => env('master.UploadUserImageSize', 2),
        'UserImageExt' => env('master.UploadUserImageSize', 'jpg,png,gif,webp'),
    ]
];