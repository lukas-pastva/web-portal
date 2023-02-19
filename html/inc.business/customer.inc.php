<?php

function businessCustomerGet(){
    return sqlGetRow("SELECT * FROM businesscustomer c LEFT JOIN sysUserbusinesscustomer uc ON c.id = uc.businesscustomerid LEFT JOIN sysUser u ON u.id = uc.sysUserid WHERE u.id = '".$_COOKIE['cookieUsername']."'");
}
