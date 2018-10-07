<?php
/**
 * Created by PhpStorm.
 * User: liwei
 * Date: 2017/4/24
 * Time: 上午12:16
 */
namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Helpers\ErrorCode;

abstract class BaseService {

    protected $errorCode;
    protected $errorMsg;
    protected $logMsg;

    public function sendErrorMessage($result, $errorCode, $errorMsg, $logMsg)
    {
        $this->errorCode = $errorCode;
        $this->errorMsg = $errorMsg;
        $this->logMsg = $logMsg;

        return $result;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }

    public function getLogMsg()
    {
        return $this->logMsg;
    }

    public function saveData($data, $table='')
    {
        if ($table)
            $this->tableName = $table;

        try {
            if ( !isset($data['created_at']))
                $data['created_at'] = time();

            DB::table($this->tableName)->insert($data);

            return true;
        }
        catch (\Exception $e) {
            return $this->sendErrorMessage(false, ErrorCode::INTERFACE_ERROR_PROPERTY, '通知失败:'.$e->getMessage(), $e->getMessage());
        }
    }

    public function delById($id, $table='')
    {
        if ($table)
            $this->tableName = $table;

        try {

            DB::table($this->tableName)->where('id', $id)->delete();;

            return true;
        }
        catch (\Exception $e) {
            return $this->sendErrorMessage(false, ErrorCode::DB_ERROR_INSERT, '添加失败:'.$e->getMessage(), $e->getMessage());
        }
    }

    public function updateById($id, $data, $table='')
    {
        if ($table)
            $this->tableName = $table;

        try {

            $data['updated_at'] = time();
            DB::table($this->tableName)->where('id', $id)->update($data);

            return true;
        }
        catch (\Exception $e) {
            return $this->sendErrorMessage(false, ErrorCode::DB_ERROR_UPDATE, '更新失败:'.$e->getMessage(), $e->getMessage());
        }
    }
}