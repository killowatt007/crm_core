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
    margin: 15px 0 5px 0;
    font-weight: bold;
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
  .block .right .text3 {
    text-align: right;
    font-style: italic;
    margin-bottom: 5px;
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
  .block-2 .right .text3 {
    font-size: 14px;
  }
</style>

<div class="body">

<div class="top-info">
  Помните, Мы поддерживаем надежность и стабильность работы оборудования в течение всего периода обслуживания.<br>
  Справки по тел: <?php echo '$this->basePhones[get_base()][top]'?> (пн - пт 09.00 - 18.00)
</div>

<div class="block block-1">
  <div class="left">
    Оплата принимается:<br>
    РНКБ Банк, во всех отделениях "Почта Крыма" . терминалы "ПэйБэрри"
    (Коммунальные платежи - Мир Домофон ) На сайте mirdomofon.com раздел<br>
    Абонентская плата.
    <div class="text">Счет</div>
    остается у абонента
  </div>
  <div class="right">
    <div class="text">Назначение платежа: оплата за обслуживание домофона&nbsp;&nbsp;&nbsp;&nbsp;Лицевой счет <?php echo str_pad($client['id'], 6, 0, STR_PAD_LEFT)?></div>
    <div class="text2">
      <div class="left2">
        БИК 043510607 РНКБ БАНК (ПАО)<br>
        Р\с 40802810342710002728<br>
        ИП Станиславский Павел Николаевич<br>
        ИНН 910300001278
      </div>
      <div class="right2">Домофон</div>
    </div>
    <div class="text3">
      Можно оплатить:за квартал <?php echo $client['RateId_j']?>руб;
      за пол года <?php echo ($client['RateId_j']*2)?>руб;
      за год <?php echo ($client['RateId_j']*4)?>руб.
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
        Начислено за <?php echo $mFrom?>-<?php echo $mTo?> 2021 г.<br>
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
    <img src="/files/qr.jpg" width="130"><br>
    <div class="text">Уведомление</div>
    остается у кассира
  </div>
  <div class="right">
    <div class="text">Назначение платежа: оплата за обслуживание домофона&nbsp;&nbsp;&nbsp;&nbsp;Лицевой счет <?php echo str_pad($client['id'], 6, 0, STR_PAD_LEFT)?></div>
    <div class="text2">
      <div class="left2">
        БИК 043510607 РНКБ БАНК (ПАО)<br>
        Р\с 40802810342710002728<br>
        ИП Станиславский Павел Николаевич<br>
        ИНН 910300001278
      </div>
      <div class="right2">Домофон</div>
    </div>
    <div class="text3">
      Справки по тел: <?php echo '$this->basePhones[get_base()][bottom]'?>
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
        Начислено за <?php echo $mFrom?>-<?php echo $mTo?> 2021 г.<br>
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