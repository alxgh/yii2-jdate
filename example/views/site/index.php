<?php

use jDate\DateTime;
/* @var $this yii\web\View */
$this->title = 'My Yii Application';
?>
<div class="site-index" dir="rtl">

    <div class="jumbotron">

        <h1>
            مثال تاریخ (تاریخ امروز):
        </h1>

        <p class="lead">
            <?php
            echo Yii::$app->jdate->jdate('Y/m/d'); // ۱۳۹۳/۰۵/۰۳
            $ShamsiDate = new DateTime();

            echo '<br />';
            echo $ShamsiDate->jdate('Y/m/d'); // ۱۳۹۳/۰۵/۰۳
            echo '<br />';
            echo $ShamsiDate->gregorian_to_jalali(2014,7,25,'/'); // 1393/5/3
            echo '<br />';
            $specialDate = $ShamsiDate->jmktime(0,0,0,1,1,1393);
            echo Yii::$app->jdate->jdate('Y/m/d', $specialDate); // ۱۳۹۳/۰۵/۰۳
            ?>
        </p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
