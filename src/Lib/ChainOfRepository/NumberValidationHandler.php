<?php
namespace App\Lib\ChainOfRepository;


class NumberValidationHandler
{
    /**
     * 自クラスが担当する処理を実行
     */
    protected function execValidation($input)
    {
        return (preg_match('/^[0-9]*$/', $input) > 0);
    }

    /**
     * 処理失敗時のメッセージを取得する
     */
    protected function getErrorMessage()
    {
        return '半角数字で入力してください';
    }
}
