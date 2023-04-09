<?php

interface Api_Lib_Pay_Pay
{
    public function start($data, $h5);

    public function notify($data);
}