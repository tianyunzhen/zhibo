<?php

class Model_UserAuth extends PhalApi_Model_NotORM
{
    const NotREVIEWED = 1;//未审核
    const ADOPT       = 2;//通过
    const NOTADOPT    = 3;//未通过
}
