<?php
$basePhones = [
  47 => ['top'=>'+7 (978) 809-92-28', 'bottom'=>'+7 (978) 809-92-28', 'post'=>'visitservis@mail.ru', 'site'=>'визитсервис.рф'], // симф
  48 => ['top'=>'+7 (978) 812-28-23; +7 (978) 812-07-76', 'bottom'=>'+7 (978) 812-28-23', 'post'=>'vizitservis2012@gmail.com', 'site'=>'VIZITSERVIS.RU']  // севас
];
$qr = [
  47 => 'qr_sim.png', // симф
  48 => 'qr_sev.png'  // севас
]; 

$basePhones = $basePhones[$bid];
$qr = $qr[$bid];

$monthItems = [
  1=>1, 2=>1, 3=>1,
  4=>2, 5=>2, 6=>2,
  7=>3, 8=>3, 9=>3,
  10=>4, 11=>4, 12=>4
];
$currentQ = $monthItems[date('n', strtotime($this->date_from))];
$year = round(((4-$currentQ)*$client['RateId_j'])+$left);
$currentDate = date('d.m.Y');

$tmpl = $this->app->getCtrl('fabrik', 'invoice_tmpl')->select('*', 'BaseId='.$bid)[0];
foreach ($tmpl as $key => $val) {
  $tmpl[$key] = str_replace(["\n", '{pay_year}', '{date_current}'], ['<br>', $year, $currentDate], $val);
}
?>

<style>
  .body {
    font-size: 12px;
  }
  .b {
    font-weight: bold;
  }
  .top-info {
    text-align: center;
    font-size: 10px;
  }

  .block .left {
    float: left;
    width: 21%;
    text-align: center;
    padding: 0 5px;
    border-right: 1px solid #000;
    
  }
  .block .left .text {
    font-size: 10px;
    margin-top: 5px;
  }
  .block .right {
    float: right;
    width: 75%;
    padding: 0 5px;
  }
  .block .right .text {
    font-weight: bold;
    margin-bottom: 5px;
    font-size: 13px;
    text-decoration: underline;
  }
  .block .right .text2 {
    font-weight: bold;
    font-size: 10px;
    margin-bottom: 3px;
  }
  .block .right .text2 .left2 {
    width: 50%;
    float: left;
  }
  .block .right .text2 .right2 {
    width: 40%;
    float: left;
    font-size: 22px;
    font-weight: bold;
    text-align: right;
    padding-top: 8px;
  }
  .block .right .text2 .right2 span {
    font-size: 14px;
    font-style: italic;
    color: #020C7E;
  }
  .block .right .text3 {
    text-align: right;
    font-style: italic;
    font-size: 15ppx;
    margin-bottom: 10px;

  }
  .block .right .text4 {
    border-top: 1px solid #000;
    border-bottom: 3px solid #000;
    margin: 0 30px;
  }
  .block .right .text5 {
    margin-top: 10px;
  }
  .block .right .text5 .left5 {
    width: 70%;
    float: left;
  }
  .block .right .text5 .right5 {
    width: 25%;
    float: right;
  }

  .block-1 {
    padding: 10px 0 20px 0;
    border-bottom: 3px dotted #000;
  }
  .block-1 .left,
  .block-1 .right {
    height: 188px;
    height: 188px;
  }

  .block-2 {
    padding-top: 20px;
  }
  .block-2 .left,
  .block-2 .right {
    height: 191px;
    height: 191px;
  }
  .block-2 .left {
    padding-top: 50px;
  }
  .block-2 .left div {
    font-weight: bold;
    margin-bottom: 10px;
  }
  .block-2 .right .text2 .left2 {
    width: 30%;
  }
  .block-2 .right .text2 .right2 {
    color: #020C7E;
    font-size: 12px;
    width: 62%;
    font-weight: 300;
    padding-top: 0px;
  }
  .block-2 .right .text3 {
    font-size: 14px;
    height: 10px;
  }
</style>

<div class="body">

<div class="top-info">
  <?php echo $tmpl['Header']?>
</div>

<div class="block block-1">
  <div class="left">
    <img src="/server/files/<?php echo $qr?>" width="130"><br>
    <div class="text">
      <?php echo $tmpl['LeftSideTop']?>
    </div>
  </div>
  <div class="right">
    <div class="text">Назначение платежа: оплата за домофон л/с <?php echo str_pad($client['id'], 6, 0, STR_PAD_LEFT)?></div>
    <div class="text2">
      <div class="left2">
        <?php echo $tmpl['Requisites']?>
      </div>
      <div class="right2">
        <span><?php echo $basePhones['site']?></span><br>
        ИП Тетерин О.М.
      </div>
    </div>
    <div class="text3">
      <?php echo $tmpl['TopText']?>
    </div>
    <div class="text4">
      Адрес:&nbsp;&nbsp;&nbsp; 
      ул. <?php echo $client['StreetId_j']?>&nbsp;&nbsp;&nbsp;
      № д. <b><?php echo $client['HouseNumber']?></b>&nbsp;&nbsp;&nbsp; 
      <?php echo $client['BuildingNumber'] ? '№ кор <b>'.$client['BuildingNumber'].'&nbsp;&nbsp;&nbsp;</b>' : '';?>
      № под. <b><?php echo $client['EntranceNumber']?></b>&nbsp;&nbsp;&nbsp;
      № кв. <b><?php echo $client['FlatNumber']?></b><br>
      <b>ФИО <?php echo $client['FIO']?></b>
    </div>
    <div class="text5">
      <div class="left5">
        <b>Задолженность за обслуживание предыдущих периодов</b><br>
        Начислено за <?php echo $mFrom?>-<?php echo $mTo?> <?php echo date('Y')?> г.<br>
        <b>Итого к оплате</b>
      </div>
      <div class="right5">
        <b><?php echo number_format($debt, 2, ',', ' ')?> руб.</b><br>
        <?php echo number_format($cinvs_sum, 2, ',', ' ')?> руб.<br>
        <b><?php echo number_format($left, 2, ',', ' ')?> руб.</b>
      </div>
    </div>
  </div>
</div>
<div class="block block-2">
  <div class="left">
    <?php echo $tmpl['LeftSideBottom']?>
  </div>
  <div class="right">
    <div class="text">Назначение платежа: оплата за домофон л/с <?php echo str_pad($client['id'], 6, 0, STR_PAD_LEFT)?></div>
    <div class="text2">
      <div class="left2">
        <?php echo $tmpl['Requisites']?>
      </div>
      <div class="right2" style="color:black;">
        <?php echo $tmpl['BottomText']?>
      </div>
    </div>



<!--     <div class="text3">
    </div> -->
    <div class="text4">
      Адрес:&nbsp;&nbsp;&nbsp; 
      ул. <?php echo $client['StreetId_j']?>&nbsp;&nbsp;&nbsp;
      № д. <b><?php echo $client['HouseNumber']?></b>&nbsp;&nbsp;&nbsp; 
      <?php echo $client['BuildingNumber'] ? '№ кор <b>'.$client['BuildingNumber'].'&nbsp;&nbsp;&nbsp;</b>' : '';?>
      № под. <b><?php echo $client['EntranceNumber']?></b>&nbsp;&nbsp;&nbsp;
      № кв. <b><?php echo $client['FlatNumber']?></b><br>
      <b>ФИО <?php echo $client['FIO']?></b>
    </div>
    <div class="text5">
      <div class="left5">
        <b>Задолженность за обслуживание предыдущих периодов</b><br>
        Начислено за <?php echo $mFrom?>-<?php echo $mTo?> <?php echo date('Y')?> г.<br>
        <b>Итого к оплате</b>
      </div>
      <div class="right5">
        <b><?php echo number_format($debt, 2, ',', ' ')?> руб.</b><br>
        <?php echo number_format($cinvs_sum, 2, ',', ' ')?> руб.<br>
        <b><?php echo number_format($left, 2, ',', ' ')?> руб.</b>
      </div>
    </div>
  </div>
</div>

</div>