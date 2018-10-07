<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2018/3/2
 * Time: 下午4:46
 */
namespace App\Http\Controllers;

use App\Helpers\ErrorCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function UploadImage(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,bmp,png',
        ]);

        $image = $request->file('file');

        if ( !$image->isValid())
            return output('', ErrorCode::UPLOAD_FILE_ERROR, '文件上传出错', $image->getError());

        $extension = $image->getClientOriginalExtension();
        $imageName = md5(time().random_int(1,10000)).".".$extension;

        try {
            $image->move(storage_path('uploads'), $imageName);
        }
        catch (\Exception $e) {
            return output('', ErrorCode::UPLOAD_FILE_ERROR, '文件存储出错', $e->getMessage());
        }


        $result = [
            'image' => $imageName,
        ];

        return output($result);
    }




}
