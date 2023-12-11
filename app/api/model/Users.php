<?php

namespace app\api\model;

use think\Model;
use think\model\concern\SoftDelete;

use app\common\Common;

class Users extends Model
{
    //开启软删除
    use SoftDelete;
    protected $deleteTime = 'deleted_at';

    //自动时间戳
    protected $autoWriteTimestamp = 'datetime';
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 设置字段信息
    protected $schema = [
        'id' => 'int',
        'number' => 'varchar(32)',
        'avatar' => 'varchar(255)',
        'email' => 'varchar(320)',
        'phone' => 'varchar(32)',
        'username' => 'varchar(255)',
        'password' => 'varchar(255)',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'int(11)',
    ];

    // 默认排除字段
    protected static $withoutField = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * 用户登录验证函数
     *
     * @param string $account 用户名、电子邮件或电话号码
     * @param string $password 密码
     * @return array 登录成功返回用户信息数组，失败返回false
     */
    public static function Login($account, $password): array
    {
        // 尝试使用用户名、电子邮件或电话号码查询用户
        $result = self::where('username', $account)
            ->whereOr('email', $account)
            ->whereOr('phone', $account)
            ->find();

        if (!$result) {
            return Common::mArrayEasyReturnStruct('用户不存在', false);
        }

        // 验证密码是否匹配
        if (!password_verify($password, $result['password'])) {
            return Common::mArrayEasyReturnStruct('密码不匹配', false, $result->toArray());
        }

        // 密码匹配，返回用户信息
        return Common::mArrayEasyReturnStruct(null, true, $result->toArray());
    }

    /**
     * 添加用户
     *
     * @param string $number
     * @param string $username
     * @param string $email
     * @param string $phone
     * @param string $password
     * @param int $status
     * @return array
     */
    public static function Register($number, $username, $email, $phone, $password, $status = 0): array
    {
        if ($password != '') {
            if ($email != '') {
                $result = self::where('email', $email)->find();
            } elseif ($phone != '') {
                $result = self::where('phone', $phone)->find();
            }
            if ($result) {
                return Common::mArrayEasyReturnStruct('邮箱或手机号已存在', false);
            } else {
                $data = array(
                    'number' => $number,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'status' => $status,
                );
            }
        } else {
            return Common::mArrayEasyReturnStruct('密码不得为空', false);
        }

        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $result = self::create($data);

        if (!$result) {
            return Common::mArrayEasyReturnStruct('数据插入失败', false);
        }

        return Common::mArrayEasyReturnStruct(null, true, $result->id);
    }

    /**
     * 读取用户列表
     *
     * @return void
     */
    public static function Index()
    {
        $result = self::select();
        if ($result) {
            return Common::mArrayEasyReturnStruct(null, true, $result->toArray());
        }
        return Common::mArrayEasyReturnStruct('列表查询失败', false);
    }

    /**
     * 读取指定ID行
     *
     * @param int $id
     * @return void
     */
    public static function Get($id)
    {
        $withoutField = self::$withoutField;
        $withoutField[] = 'password';
        $result = self::where('id', $id)->withoutField($withoutField)->find();
        if ($result) {
            return Common::mArrayEasyReturnStruct(null, true, $result);
        }
        return Common::mArrayEasyReturnStruct('项目查询失败', false);
    }

    /**
     * 删除指定ID行
     *
     * @param int $id
     * @return void
     */
    public static function Del($id)
    {
        $result = self::delete($id);
        if ($result) {
            return Common::mArrayEasyReturnStruct(null, true, $result);
        }
        return Common::mArrayEasyReturnStruct('项目删除失败', false);
    }
}