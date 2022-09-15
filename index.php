<?php
  //エラーログの設定
  ini_set('log_errors','on');
  ini_set('error_log','php.log');

  //セッション開始
  session_start();

  //デバッグ設定
  $debug_flag = false;

  function debug($str){
    global $debug_flag;

    if(!empty($debug_flag)){
      error_log('デバッグ: '.$str);
    }
  }

  //昆虫格納用
  $insects_01 = array();
  $insects_02 = array();
  $insects_03 = array();
  $insects_04 = array();
  $insects_05 = array();

  //昆虫のレア度(各確率)
  class Rarity{
    const R01 = 30;
    const R02 = 25;
    const R03 = 20;
    const R04 = 15;
    const R05 = 10; 
  }

  //表示メッセージ
  interface MessageInterface{
    public static function set($str);
    public static function clear();
  }
  class Message implements MessageInterface{
    public static function set($str){
      if(empty($_SESSION['message'])){
        $_SESSION['message'] = '';
      }
      $_SESSION['message'] .= $str.'<br>';
    }

    public static function clear(){
      unset($_SESSION['message']);
    }
  }

  //確率計算
  interface PercentageInterface{
    public static function calc($cage);
  }

  class Percentage implements PercentageInterface{
    public static function calc($cage){
      global $insects_01;
      global $insects_02;
      global $insects_03;
      global $insects_04;
      global $insects_05;

      //試行回数
      $times = $cage->getTrial();

      //捕まりやすさ(確率)
      $cap = $cage->getCapturable() / 100;
      //逃げやすさ(確率)
      $esc = $cage->getEscapable() / 100;

      //レア度・種類(確率)
      switch($cage->getRarity()){
        case 1:
          $rare = Rarity::R01 / 100;
          $spe = 1 / count($insects_01);
          switch($cage->getSize()){
            case '大':
              $size = Insect_01::L / 100;
              break;
            case '中':
              $size = Insect_01::M / 100;
              break;
            case '小':
              $size = Insect_01::S / 100;
              break;
          }
          break;

        case 2:
          $rare = Rarity::R02 / 100;
          $spe = 1 / count($insects_02);
          switch($cage->getSize()){
            case '大':
              $size = Insect_02::L / 100;
              break;
            case '中':
              $size = Insect_02::M / 100;
              break;
            case '小':
              $size = Insect_02::S / 100;
              break;
          }
          break;

        case 3:
          $rare = Rarity::R03 / 100;
          $spe = 1 / count($insects_03);
          switch($cage->getSize()){
            case '極大':
              $size = Insect_03::XL / 100;
              break;
            case '大':
              $size = Insect_03::L / 100;
              break;
            case '中':
              $size = Insect_03::M / 100;
              break;
            case '小':
              $size = Insect_03::S / 100;
              break;
            case '極小':
              $size = Insect_03::XS / 100;
              break;
          }
          break;

        case 4:
          $rare = Rarity::R04 / 100;
          $spe = 1 / count($insects_04);
          switch($cage->getSize()){
            case '極大':
              $size = Insect_04::XL / 100;
              break;
            case '大':
              $size = Insect_04::L / 100;
              break;
            case '中':
              $size = Insect_04::M / 100;
              break;
            case '小':
              $size = Insect_04::S / 100;
              break;
            case '極小':
              $size = Insect_04::XS / 100;
              break;
          }
          break;

        case 5:
          $rare = Rarity::R05 / 100;
          $spe = 1 / count($insects_05);
          switch($cage->getSize()){
            case '極大':
              $size = Insect_05::XL / 100;
              break;
            case '大':
              $size = Insect_05::L / 100;
              break;
            case '中':
              $size = Insect_05::M / 100;
              break;
            case '小':
              $size = Insect_05::S / 100;
              break;
            case '極小':
              $size = Insect_05::XS / 100;
              break;
          }
          break;
      }

      return $rare*$spe*$size*((1 - $cap)**($times - 1))*((1 - $esc)**($times - 1))*$cap*100;
    }
  }

  //虫かごクラス
  class Cage{
    protected $name;  //名前
    protected $img;  //画像
    protected $size;  //大きさ
    protected $rarity;  //レア度
    protected $trial;  //試行回数
    protected $capturable;  //捕まりやすさ(%)
    protected $escapable;  //逃げやすさ(%)

    public function __construct($name,$img,$size,$rarity,$trial,$capturable,$escapable){
      $this->name = $name;
      $this->img = $img;
      $this->size = $size;
      $this->rarity = $rarity;
      $this->trial = $trial;
      $this->capturable = $capturable;
      $this->escapable = $escapable;
    }

    //ゲッター
    public function getName(){
      return $this->name;
    }
    public function getImg(){
      return $this->img;
    }
    public function getSize(){
      return $this->size;
    }
    public function getRarity(){
      return $this->rarity;
    }
    public function getTrial(){
      return $this->trial;
    }
    public function getCapturable(){
      return $this->capturable;
    }
    public function getEscapable(){
      return $this->escapable;
    }

    //セッター
    public function setName($str){
      $this->name = $str;
    }
    public function setImg($path){
      $this->img = $path;
    }
    public function setSize($str){
      $this->size = $str;
    }
    public function setRarity($int){
      $this->rarity = $int;
    }
    public function setTrial($int){
      $this->trial = $int;
    }
    public function setCapturable($int){
      $this->capturable = $int;
    }
    public function setEscapable($int){
      $this->escapable = $int;
    }

    public function detain($insect){
      $this->setName($insect->getName());
      $this->setImg($insect->getImg());
      $this->setSize($insect->getSize());
      $this->setRarity($insect->getRarity());
      $this->setTrial($insect->getTrial());
      $this->setCapturable($insect->getCapturable());
      $this->setEscapable($insect->getEscapable());
    }

    public function release(){
      $this->setName(null);
      $this->setImg(null);
      $this->setSize(null);
      $this->setRarity(null);
      $this->setTrial(null);
      $this->setCapturable(null);
      $this->setEscapable(null);
    }
  }

  //昆虫クラス
  class Insect_01 extends Cage{
    //大きさ定数(%)
    const L = 30;
    const M = 40;
    const S = 30;

    //大きさ決定関数
    public static function sizeSelector(){
      $size_array = array(
        '大' => self::L,
        '中' => self::M,
        '小' => self::S,
      );
    
      $sum  = array_sum($size_array);
      $rand = mt_rand(1, $sum);

      foreach($size_array as $key => $weight){
          if (($sum -= $weight) < $rand){
              return $key;
          }
      }
    }

    //逃げずにいる
    public function stay(){
      Message::set($this->getName().'はとどまっている');
    }
  }

  class Insect_02 extends Insect_01{
    //大きさ定数(%)
    const L = 25;
    const M = 50;
    const S = 25;

    //大きさ決定関数
    public static function sizeSelector(){
      $size_array = array(
        '大' => self::L,
        '中' => self::M,
        '小' => self::S,
      );
    
      $sum  = array_sum($size_array);
      $rand = mt_rand(1, $sum);

      foreach($size_array as $key => $weight){
          if (($sum -= $weight) < $rand){
              return $key;
          }
      }
    }

    //逃げる
    public function escape(){
      Message::set($this->getName().'はにげてしまった');
    }
  }

  class Insect_03 extends Insect_02{
    //大きさ定数(%)
    const XL = 15;
    const L = 20;
    const M = 30;
    const S = 20;
    const XS = 15;

    //大きさ決定関数
    public static function sizeSelector(){
      $size_array = array(
        '極大' => self::XL,
        '大' => self::L,
        '中' => self::M,
        '小' => self::S,
        '極小' => self::XS
      );
    
      $sum  = array_sum($size_array);
      $rand = mt_rand(1, $sum);

      foreach($size_array as $key => $weight){
          if (($sum -= $weight) < $rand){
              return $key;
          }
      }
    }
  }

  class Insect_04 extends Insect_03{
    //大きさ定数(%)
    const XL = 12;
    const L = 23;
    const M = 30;
    const S = 23;
    const XS = 12;

    //大きさ決定関数
    public static function sizeSelector(){
      $size_array = array(
        '極大' => self::XL,
        '大' => self::L,
        '中' => self::M,
        '小' => self::S,
        '極小' => self::XS
      );
    
      $sum  = array_sum($size_array);
      $rand = mt_rand(1, $sum);

      foreach($size_array as $key => $weight){
          if (($sum -= $weight) < $rand){
              return $key;
          }
      }
    }
  }

  class Insect_05 extends Insect_04{
    //大きさ定数(%)
    const XL = 10;
    const L = 20;
    const M = 40;
    const S = 20;
    const XS = 10;

    //大きさ決定関数
    public static function sizeSelector(){
      $size_array = array(
        '極大' => self::XL,
        '大' => self::L,
        '中' => self::M,
        '小' => self::S,
        '極小' => self::XS
      );
    
      $sum  = array_sum($size_array);
      $rand = mt_rand(1, $sum);

      foreach($size_array as $key => $weight){
          if (($sum -= $weight) < $rand){
              return $key;
          }
      }
    }
  }

  //昆虫生成
  $insects_01[] = new Insect_01('バッタ','img/img_03.png',Insect_01::sizeSelector(),1,0,80,0);
  $insects_01[] = new Insect_01('テントウムシ','img/img_07.png',Insect_01::sizeSelector(),1,0,70,0);
  $insects_01[] = new Insect_01('セミ','img/img_16.png',Insect_01::sizeSelector(),1,0,60,0);

  $insects_02[] = new Insect_02('ナミテントウ','img/img_12.png',Insect_02::sizeSelector(),2,0,70,5);
  $insects_02[] = new Insect_02('カマキリ','img/img_05.png',Insect_02::sizeSelector(),2,0,75,3);
  $insects_02[] = new Insect_02('スズムシ','img/img_13.png',Insect_02::sizeSelector(),2,0,80,10);

  $insects_03[] = new Insect_03('アゲハチョウ','img/img_01.png',Insect_03::sizeSelector(),3,0,60,15);
  $insects_03[] = new Insect_03('モンシロチョウ','img/img_02.png',Insect_03::sizeSelector(),3,0,65,10);
  $insects_03[] = new Insect_03('アカトンボ','img/img_14.png',Insect_03::sizeSelector(),3,0,55,20);
  $insects_03[] = new Insect_03('ホタル','img/img_11.png',Insect_03::sizeSelector(),3,0,50,25);

  $insects_04[] = new Insect_04('カブトムシ','img/img_06.png',Insect_04::sizeSelector(),4,0,60,25);
  $insects_04[] = new Insect_04('モルフォチョウ','img/img_10.png',Insect_04::sizeSelector(),4,0,50,20);
  $insects_04[] = new Insect_04('クワガタムシ','img/img_15.png',Insect_04::sizeSelector(),4,0,55,20);
  $insects_04[] = new Insect_04('オニヤンマ','img/img_08.png',Insect_04::sizeSelector(),4,0,40,30);

  $insects_05[] = new Insect_05('オウゴンクワガタ','img/img_09.png',Insect_05::sizeSelector(),5,0,30,40);
  $insects_05[] = new Insect_05('ヘラクレスオオカブト','img/img_04.png',Insect_05::sizeSelector(),5,0,35,35);

  //個体群抽選関数
  function raritySelector(){
    $size_array = array(
      5 => Rarity::R05,
      4 => Rarity::R04,
      3 => Rarity::R03,
      2 => Rarity::R02,
      1 => Rarity::R01
    );
  
    $sum  = array_sum($size_array);
    $rand = mt_rand(1, $sum);

    foreach($size_array as $key => $weight){
        if (($sum -= $weight) < $rand){
            return $key;
        }
    }
  }

  //個体生成関数
  function encountInsect(){
    global $insects_01;
    global $insects_02;
    global $insects_03;
    global $insects_04;
    global $insects_05;

    global $success_flag;
    global $escape_flag;

    $success_flag = false;
    $escape_flag = false;
    $_SESSION['try_num'] = 0;
    $_SESSION['found_num'] += 1;

    //各個体群を選び、その中から個体を生成する
    $rarity = raritySelector();
    switch($rarity){
      case 1:
        $_SESSION['insect'] = $insects_01[mt_rand(0,count($insects_01) - 1)];
        break;
      case 2:
        $_SESSION['insect'] = $insects_02[mt_rand(0,count($insects_02) - 1)];
        break;
      case 3:
        $_SESSION['insect'] = $insects_03[mt_rand(0,count($insects_03) - 1)];
        break;
      case 4:
        $_SESSION['insect'] = $insects_04[mt_rand(0,count($insects_04) - 1)];
        break;
      case 5:
        $_SESSION['insect'] = $insects_05[mt_rand(0,count($insects_05) - 1)];
        break;
    }

    Message::set($_SESSION['insect']->getName().'を見つけた');
  }

  function init(){
    Message::clear();
    Message::set('ゲームスタート');
    $_SESSION['cage1'] = new Cage(null,null,null,null,null,null,null);
    $_SESSION['cage2'] = new Cage(null,null,null,null,null,null,null);
    $_SESSION['cage3'] = new Cage(null,null,null,null,null,null,null);
    $_SESSION['cage_ex'] = new Cage(null,null,null,null,null,null,null);

    $_SESSION['found_num'] = 0;
    $_SESSION['captured_num'] = 0;

    encountInsect();
  }

  function quit(){
    $_SESSION = array();
  }



  if(!empty($_POST)){
    $start_flag = (!empty($_POST['start'])) ? true : false;
    $capture_flag = (!empty($_POST['capture'])) ? true : false;
    $run_flag = (!empty($_POST['run'])) ? true : false;
    $restart_flag = (!empty($_POST['restart'])) ? true : false;
    $quit_flag = (!empty($_POST['quit'])) ? true : false;
    $observe_flag = (!empty($_POST['observe'])) ? true : false;
    $cage1_observe = (!empty($_POST['observe_cage1'])) ? true : false;
    $cage2_observe = (!empty($_POST['observe_cage2'])) ? true : false;
    $cage3_observe = (!empty($_POST['observe_cage3'])) ? true : false;
    $cage1_release = (!empty($_POST['release_cage1'])) ? true : false;
    $cage2_release = (!empty($_POST['release_cage2'])) ? true : false;
    $cage3_release = (!empty($_POST['release_cage3'])) ? true : false;
    $cage_ex_release = (!empty($_POST['release_cage_ex'])) ? true : false;

    $cage_full = false;

    if($start_flag){
      debug('ゲームスタート');
      init();
      
    }elseif($capture_flag){
      $_SESSION['try_num'] += 1;
      $_SESSION['insect']->setTrial($_SESSION['try_num']);

      //$capturableの値に従って捕まるかどうか決定する
      if(mt_rand(1,100) <= $_SESSION['insect']->getCapturable()){
        $success_flag = true;

      }else{
        //捕まらなかったとき、$escapableの値に従って逃げるかどうか決定する
        if(mt_rand(1,100) <= $_SESSION['insect']->getEscapable()){
          $_SESSION['insect']->escape();
          $escape_flag = true;

        }else{
          $_SESSION['insect']->stay();
        }
      }
      

      if(!empty($success_flag)){
        $_SESSION['captured_num'] += 1;

        Message::set($_SESSION['insect']->getName().'をつかまえた');
        if(empty($_SESSION['cage1']->getName())){
          $_SESSION['cage1']->detain($_SESSION['insect']);
          Message::set('上の虫かごに入れました');
  
        }elseif(empty($_SESSION['cage2']->getName())){
          $_SESSION['cage2']->detain($_SESSION['insect']);
          Message::set('真ん中の虫かごに入れました');
  
        }elseif(empty($_SESSION['cage3']->getName())){
          $_SESSION['cage3']->detain($_SESSION['insect']);
          Message::set('下の虫かごに入れました');
  
        }else{
          Message::set('虫かごがいっぱいです');
          Message::set('にがすのを1つえらんでください');
          $cage_full = true;
          $_SESSION['cage_ex']->detain($_SESSION['insect']);
        }
      }

    }elseif($run_flag){
      //にげる
      debug('逃げた');
      encountInsect();

    }elseif($restart_flag){
      //さらに探す
      debug('さらに探す');
      encountInsect();

    }elseif($observe_flag){
      //かんさつする
      debug('観察する');

    }elseif($quit_flag){
      //やめる
      debug('やめる');
      quit();

    }elseif($cage1_observe){
      debug('上の虫かごをかんさつ');
      $_SESSION['insect'] = $_SESSION['cage1'];

    }elseif($cage2_observe){
      debug('真ん中の虫かごをかんさつ');
      $_SESSION['insect'] = $_SESSION['cage2'];

    }elseif($cage3_observe){
      debug('下の虫かごをかんさつ');
      $_SESSION['insect'] = $_SESSION['cage3'];

    }elseif($cage1_release){
      debug('上の虫をにがした');
      Message::set($_SESSION['cage1']->getName().'をにがした');
      $_SESSION['insect'] = clone $_SESSION['cage1'];
      $_SESSION['cage1']->detain($_SESSION['cage_ex']);
      $_SESSION['cage_ex']->release();
      $cage_full = false;

    }elseif($cage2_release){
      debug('真ん中の虫をにがした');
      Message::set($_SESSION['cage2']->getName().'をにがした');
      $_SESSION['insect'] = clone $_SESSION['cage2'];
      $_SESSION['cage2']->detain($_SESSION['cage_ex']);
      $_SESSION['cage_ex']->release();
      $cage_full = false;

    }elseif($cage3_release){
      debug('下の虫をにがした');
      Message::set($_SESSION['cage3']->getName().'をにがした');
      $_SESSION['insect'] = clone $_SESSION['cage3'];
      $_SESSION['cage3']->detain($_SESSION['cage_ex']);
      $_SESSION['cage_ex']->release();
      $cage_full = false;

    }elseif($cage_ex_release){
      debug('今つかまえた虫をにがした');
      Message::set($_SESSION['cage_ex']->getName().'をにがした');
      $_SESSION['cage_ex']->release();
      $cage_full = false;

    }


    $_POST = array();
  }

  
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>昆虫採集ゲーム</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="site_width">
    <?php if(empty($_SESSION)){ ?>
      <h2 class="title">昆虫採集ゲーム</h2>
      <div class="main">
        <div class="start_content_wrapper">
          <h3 class="sub_title">GAME START ?</h3>
          <form action="" method="post" class="start_command_wrapper">
            <input type="submit" value="▷スタート" name="start" class="start_command">
          </form>
        </div>
      </div>
    <?php }else{ ?>
      <div class="main">
      <?php if(!empty($cage1_observe) || !empty($cage2_observe) || !empty($cage3_observe)){ ?>
        <h3 class="sub_title insect_detect">であってつかまえられる確率は&nbsp;<?php echo Percentage::calc($_SESSION['insect']); ?>%!!</h3>
      <?php }elseif(!empty($cage1_release) || !empty($cage2_release) || !empty($cage3_release) || !empty($cage_ex_release)){ ?>
        <h3 class="sub_title insect_detect"><?php echo $_SESSION['insect']->getName(); ?>をにがした</h3>
      <?php }elseif(!empty($escape_flag)){ ?>
        <h3 class="sub_title insect_detect"><?php echo $_SESSION['insect']->getName(); ?>ににげられた</h3>
      <?php }else{ ?>
        <h3 class="sub_title insect_detect"><?php echo $_SESSION['insect']->getName(); ?>を<?php echo (!empty($capture_flag) && !empty($success_flag)) ? 'つかまえた！' : '見つけた！'; ?></h3>
      <?php } ?>
        <div class="insect_wrapper">
          <div class="count_wrapper">
            <div class="found_result">
              <p class="text_centered">みつけた</p>
              <p class="text_centered"><?php echo $_SESSION['found_num']; ?></p>
            </div>
            <div class="captured_result">
              <p class="text_centered">つかまえた</p>
              <p class="text_centered"><?php echo $_SESSION['captured_num']; ?></p>
            </div>
          </div>
          <div class="img_wrapper">
          <?php if(!empty($capture_flag) && !empty($success_flag) || !empty($observe_flag)){ ?>
            <img src="img/img_ami.png" alt="つかまえた" class="img">
          <?php }elseif(!empty($escape_flag) || !empty($cage1_release) || !empty($cage2_release) || !empty($cage3_release) || !empty($cage_ex_release)){ ?>

          <?php }else{ ?>
            <img src="<?php echo $_SESSION['insect']->getImg(); ?>" alt="<?php echo $_SESSION['insect']->getName(); ?>" class="img">
          <?php } ?>
          </div>
        <?php if(!empty($_SESSION['cage1']->getName()) || !empty($_SESSION['cage2']->getName()) || !empty($_SESSION['cage3']->getName())){ ?>
          <div class="cage_all_wrapper">
          <?php if(!empty($observe_flag || $cage_full)){ ?>
            <form action="" method="post">
          <?php } ?>
          <?php if(!empty($_SESSION['cage1']->getName())){ ?>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <label>
            <?php } ?>
                <div class="cage_wrapper">
                  <img src="<?php echo $_SESSION['cage1']->getImg(); ?>" alt="<?php echo $_SESSION['cage1']->getName(); ?>" class="img">
                </div>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <?php if(!empty($observe_flag)){ ?>
                <input type="submit" value="cage1" name="observe_cage1" style="display: none;">
              <?php }elseif(!empty($cage_full)){ ?>
                <input type="submit" value="cage1" name="release_cage1" style="display: none;">
              <?php } ?>
              </label>
            <?php } ?>
          <?php } ?>
          <?php if(!empty($_SESSION['cage2']->getName())){ ?>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <label>
            <?php } ?>
                <div class="cage_wrapper">
                  <img src="<?php echo $_SESSION['cage2']->getImg(); ?>" alt="<?php echo $_SESSION['cage2']->getName(); ?>" class="img">
                </div>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <?php if(!empty($observe_flag)){ ?>
                <input type="submit" value="cage2" name="observe_cage2" style="display: none;">
              <?php }elseif(!empty($cage_full)){ ?>
                <input type="submit" value="cage2" name="release_cage2" style="display: none;">
              <?php } ?>
              </label>
            <?php } ?>
          <?php } ?>
          <?php if(!empty($_SESSION['cage3']->getName())){ ?>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <label>
            <?php } ?>
                <div class="cage_wrapper">
                  <img src="<?php echo $_SESSION['cage3']->getImg(); ?>" alt="<?php echo $_SESSION['cage3']->getName(); ?>" class="img">
                </div>
            <?php if(!empty($observe_flag || $cage_full)){ ?>
              <?php if(!empty($observe_flag)){ ?>
                <input type="submit" value="cage3" name="observe_cage3" style="display: none;">
              <?php }elseif(!empty($cage_full)){ ?>
                <input type="submit" value="cage3" name="release_cage3" style="display: none;">
              <?php } ?>
              </label>
            <?php } ?>
          <?php } ?>
          <?php if(!empty($_SESSION['cage_ex']->getName())){ ?>
            <?php if(!empty($cage_full)){ ?>
              <label>
            <?php } ?>
                <div class="cage_ex_wrapper">
                  <img src="<?php echo $_SESSION['cage_ex']->getImg(); ?>" alt="<?php echo $_SESSION['cage_ex']->getName(); ?>" class="img">
                </div>
            <?php if(!empty($cage_full)){ ?>
                <input type="submit" value="cage_ex" name="release_cage_ex" style="display: none;">
              </label>
            <?php } ?>
          <?php } ?>
          <?php if(!empty($observe_flag || $cage_full)){ ?>
            </form>
          <?php } ?>
          </div>
        <?php } ?>
        </div>
      <?php if(!empty($cage1_observe) || !empty($cage2_observe) || !empty($cage3_observe)){ ?>
        <div class="result_height">
          <p><?php echo $_SESSION['insect']->getName(); ?></p>
          <p><?php echo $_SESSION['insect']->getSize(); ?></p>
          <p>★×<?php echo $_SESSION['insect']->getRarity(); ?></p>
        </div>
      <?php }elseif(!empty($observe_flag)){ ?>
        <div class="status_box indicate_height">
          <p class="text_centered">かんさつする虫かごをえらんでください</p>
        </div>
      <?php }elseif(!empty($cage_full)){ ?>
        <div class="status_box indicate_height">
          <p class="text_centered">虫かごがいっぱいです</p>
          <p class="text_centered">にがす虫1つを選んでください</p>
        </div>
      <?php }elseif((!empty($capture_flag) && !empty($success_flag)) || (!empty($cage1_release) || !empty($cage2_release) || !empty($cage3_release) || !empty($cage_ex_release)) || !empty($escape_flag)){ ?>
        <div class="status_box indicate_height">
          <form action="" method="post" class="success_command_wrapper">
            <input type="submit" value="▷<?php if(empty($escape_flag)) echo 'さらに'; ?>昆虫をさがす" name="restart">
          <?php if(empty($escape_flag)){ ?>
            <input type="submit" value="▷かんさつする" name="observe">
          <?php } ?>
            <input type="submit" value="▷ゲームをやめる" name="quit">
          </form>
        </div>
      <?php }else{ ?>
        <div class="indicate_height">
        <?php if($_SESSION['try_num'] > 0){ ?>
          <p class="text_centered">つかまえられなかった</p>
          <p class="text_centered"><?php echo $_SESSION['try_num']+1; ?>度目のチャンス</p>
        <?php } ?>
          <form action="" method="post" class="capture_command_wrapper">
            <input type="submit" value="▷つかまえる" name="capture">
            <input type="submit" value="▷つかまえない" name="run">
          </form>
        </div>
      <?php } ?>
      <?php if(!empty($cage1_observe) || !empty($cage2_observe) || !empty($cage3_observe)){ ?>
        <div class="observe_command_height">
          <form action="" method="post" class="observe_command_wrapper">
            <input type="submit" value="▷さらにかんさつする" name="observe">
            <input type="submit" value="▷さらに昆虫をさがす" name="restart">
            <input type="submit" value="▷ゲームをやめる" name="quit">
          </form>
        </div>
      <?php } ?>
        <div class="msg_box">
          <p><?php echo $_SESSION['message']; ?></p>
        </div>
      </div>
    <?php } ?>
    </div>
  </body>
</html>