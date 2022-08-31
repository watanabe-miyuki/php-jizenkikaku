<?php
require('db_connect.php');

try {
    // 全てのエージェントの掲載ステータスをupdateする。
    date_default_timezone_set('Asia/Tokyo');
    $today = date("Y-m-d");
    // 掲載再開
    $stmt = $db->prepare('update agents set list_status=1 where started_at <= :started_at and ended_at >= :ended_at');
    $stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
    $stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // 申し込み上限数到達(今月の申し込み数と比較)
    // 全てのエージェントでforeach
    // 全てのエージェント
    $stmt = $db->query('select id from agents');
    $stmt->execute();
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 今月の申し込み数
    foreach ($agents as $agent) {
        $stmt = $db->prepare('SELECT * FROM students AS S, students_contacts AS SC, agents AS A WHERE S.id = SC.student_id AND SC.agent_id = A.id AND SC.agent_id = :agent_id AND DATE_FORMAT(S.created, "%Y-%m") = :form_month ');
        $stmt->bindValue(':form_month', Date('Y-m'), PDO::PARAM_STR);
        $stmt->bindValue(':agent_id', $agent['id'], PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cnt = count($result);
        // 比較
        $stmt = $db->prepare('update agents set list_status=3 where id= :id and application_max <= :application');
        $stmt->bindValue(':id', $agent['id'], PDO::PARAM_INT);
        $stmt->bindValue(':application', $cnt, PDO::PARAM_INT);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }

        // タグ不足
        $stmt = $db->prepare('select tag_id from agents_tags where agent_id=:id');
        $stmt->bindValue(':id', (int)$agent['id'], PDO::PARAM_INT);
        $stmt->execute();
        $agent_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!$agent_tags) {
            $tag_lack = $agent['id'];
        } else {
            foreach ($agent_tags as $agent_tag) {
                $stmt = $db->prepare('select sort_id from filter_tags where tag_id=:tag_id');
                $stmt->bindValue(':tag_id', $agent_tag, PDO::PARAM_STR);
                $stmt->execute();
                $tags[] = $stmt->fetch(PDO::FETCH_COLUMN);
            }
            //タグ情報
            $stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
            $filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // var_dump($tags);
            foreach ($filter_sorts_tags as $f) {
                // var_dump($f['id']);
                if (!in_array($f['id'], $tags)) {
                    $tag_lack = $agent['id'];
                }
            }
        }
        $stmt = $db->prepare('update agents set list_status=4 where id= :id');
        if (isset($tag_lack)) {
            $stmt->bindValue(':id', $tag_lack, PDO::PARAM_INT);
            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }
        }
    }

    // 掲載期間外
    $stmt = $db->prepare('update agents set list_status=2 where started_at > :started_at or ended_at < :ended_at');
    $stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
    $stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
    $stmt->execute();
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }
    // upadateここまで

    $stmt = $db->query('select * from agents where list_status=1 ORDER BY id desc');
    $listed_agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '接続失敗';
    $e->getMessage();
    exit();
};

//タグ情報
$stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
$filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t_list = [];
foreach ($filter_sorts_tags as $f) {
    $t_list[(int)$f['id']][] = $f;
}

// タグ表示テスト　htmlの上に各部分
$stmt = $db->query('select agent_id, at.tag_id, sort_id, tag_name from agents_tags at, filter_tags ft where at.tag_id = ft.tag_id');
$agents_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$at_list = [];

// var_dump($agents_tags[0]);
foreach ($agents_tags as $a) {
    $at_list[(int)$a['agent_id']][] = $a;
}

// 問い合わせから戻ったセッション
if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['back_index'])) {
    $student_contacts = $_SESSION['back_index'];
    // var_dump($student_contacts);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>CRAFT</title>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>

<body>
    <!-- ヘッダー -->
    <header>
        <img src="logo.png" alt="">
        <!-- <nav>
            <ul>
                <li><a href="#">就活サイト</a></li>
                <li><a href="#">就活支援サービス</a></li>
                <li><a href="#">就活の教科書とは</a></li>
                <li><a href="#">お問い合わせ</a></li>
            </ul>
        </nav> -->
        <button type="button" class="btn js-btn">
            <span class="btn-line">
                <span>絞り込む</span>
            </span>
        </button>
        <nav>
            <ul class="menu2">
                <li><a href="#">就活サイト</a></li>
                <li><a href="#">就活支援</a></li>
                <li><a href="#">就活の教科書とは</a></li>
                <li><a href="#">お問い合わせ</a></li>
            </ul>
        </nav>
    </header>

    <!-- <nav id="global-nav">
        <div class="inner">
            <ul class="global-list">
                <li class="global-item"><a href="#section1">セクション１</a></li>
                <li class="global-item"><a href="#section2">セクション２</a></li>
                <li class="global-item"><a href="#section3">セクション３</a></li>
                <li class="global-item"><a href="#section4">セクション４</a></li>
                <li class="global-item"><a href="#section5">セクション５</a></li>
            </ul>
        </div>
    </nav> -->

    <wrapper>
        <div class="first_message ">
            <div class="bkRGBA">
                <div class="word fade-in-bottom">
                    <h1>CRAFT</h1>
                    <h2>気軽に<span class="emphasis">複数</span>のエージェント選びを</h2>
                </div>
            </div>
        </div>
        <p class="easy_step1"><span class="easy_step2">問い合わせは簡単<span class="easy_step3">４</span>ステップ！</span></p>
        <div class="process">
            <p class="slide_in_1">絞り込む</p>
            <div class="arrow slide_in_2"></div>
            <p class="slide_in_3">比較する</p>
            <div class="arrow slide_in_4"></div>
            <p class="slide_in_5">キープする</p>
            <div class="arrow slide_in_6"></div>
            <p class="slide_in_7">問い合わせる</p>
        </div>
        <div class="q_and_a">
            <p>Q.いくつのエージェントを問い合わせればいいの？</p>
            <br>
            <p>A. <span class="multiples">複数</span>のエージェントに問い合わせることをおすすめしています。</p>
            <p>理由としては、以下のようなものが挙げられます。</p>
            <br>
            <br>
            <div class="reason">
                <p>・<span>目的</span>に合わせてエージェントを使い分けられる</p>
                <p>・様々な<span>視点</span>からアドバイスをもらえる</p>
                <p>・応募できる<span>求人の幅</span>を広げることができる</p>
            </div>
        </div>
        <img src="agent_person.png" alt="" class="agent_person">

        <h3 class="agent_all_title">エージェント一覧</h3>
        <container class="filter" id="js-filter">
            <!-- 各エージェント -->
            <ul class="filter-items">
                <form action="entry.php" method="post" id="inquiry_submit">
                    <?php foreach ($listed_agents as $listed_agent) : ?>
                        <?php foreach ($at_list as $agent_tags) : ?>
                            <?php if ($listed_agent['id'] === current($agent_tags)['agent_id']) : ?>

                                <li class="agent_box js_target" id="tohoku_<?php echo $listed_agent['id'] ?>" <?php
                                                                                                                $tag_name = "";
                                                                                                                foreach ($agent_tags as $index => $agent_tag) {
                                                                                                                    if ($tag_name == "") {
                                                                                                                        $tag_name = $agent_tag['tag_name'];
                                                                                                                    } else {
                                                                                                                        $tag_name .= ',' . $agent_tag['tag_name'];
                                                                                                                    }
                                                                                                                    if ($agent_tags[$index]['sort_id'] != $agent_tags[$index + 1]['sort_id']) {

                                                                                                                        echo "data-"  . $agent_tag['sort_id'] . "=" . "'" . $tag_name . "'";
                                                                                                                        $tag_name = "";
                                                                                                                    }
                                                                                                                }
                                                                                                                ?>>



                                    <img class="agent_img" src="img/insert_logo/<?php echo $listed_agent['insert_logo'] ?>" alt="企業ロゴ">
                                    <div class="agent_article">
                                        <div class="agent_article_header">
                                            <h1 class="agent_name"><?php echo $listed_agent['insert_company_name'] ?></h1>
                                            <p class="num_company">取扱企業数：<?php echo $listed_agent['insert_handled_number'] ?></p>
                                        </div>
                                        <div class="agent_article_main">
                                            <div class="agent_type">
                                                <!--  タグ表示↓ -->
                                                <?php foreach ($agent_tags as $agent_tag) : ?>
                                                    <p class="agent_tag ">
                                                        #<?= $agent_tag['tag_name']; ?>
                                                    </p>
                                                <?php endforeach; ?>
                                                <!--  タグ表示↑ -->
                                            </div>
                                            <p class="recommend_points">特徴</p>
                                            <div class="recommend_points_box">
                                                <p><?php echo $listed_agent['insert_recommend_1'] ?></p>
                                            </div>
                                            <div class="recommend_points_box">
                                                <p><?php echo $listed_agent['insert_recommend_2'] ?></p>
                                            </div>
                                            <div class="recommend_points_box">
                                                <p><?php echo $listed_agent['insert_recommend_3'] ?></p>
                                            </div>
                                        </div>

                                        <div class="agent_article_footer">
                                            <p class="span_published">掲載期間：<?php echo date("Y/m/d", strtotime($listed_agent['started_at'])); ?>〜<?php echo date("Y/m/d", strtotime($listed_agent['ended_at'])); ?></p>
                                            <label id="tohoku_<?php echo $listed_agent['id'] ?>">
                                                <input id="keep_<?php echo $listed_agent['id'] ?>" class="bn632-hover bn19 " onclick="check(<?php echo $listed_agent['id'] ?>)" type=checkbox name=student_contacts[] value="<?php echo $listed_agent['id']; ?>"><span class="for_keep_btn"></span>
                                            </label>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </form>
            </ul>

            <!-- フィルター -->
                <ul class="menu">
                    <div class="filter_left_wrapper">
                        <div class="filter-cond" id="filter_side">
                            <div id="select">
                                <p class="filter_num_all">
                                    <span class="filter_num  js_numerator"></span>件／全<span class="el_searchResult js_denominator"></span>件
                                </p>
                                <div class="filter_box">
                                    <p class="filter_script">絞り込み条件</p>
                                    <?php foreach ($t_list as $filter_sort) : ?>
                                        <div class="filter_sort_name"><?= current($filter_sort)['sort_name']; ?></div>
                                        <div class="each_filter_box js_conditions" data-type="<?= current($filter_sort)['id']; ?>">
                                            <?php foreach ($filter_sort as $filter_tag) : ?>
                                                <span class="w bl_selectBlock_check ">
                                                    <label class="added-tag ">
                                                        <input onclick="scrollBlue()" type="checkbox" name="agent_tags[]" class="checks" id="form" value="<?= $filter_tag['tag_name'] ?>" />
                                                        <?= $filter_tag['tag_name']; ?>
                                                    </label>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="filter_btn">
                                    <div class="flex_btn">
                                        <div onclick="scrollBlue()" class="reset_btn  js_release" id="uncheck-btn" type="reset">リセット</div>
                                    </div>
                                    <button class="trigger_keep_btn btn_gray" id="trigger_keep_btn"><label for="trigger_keep"><span id="counter_dis">
                                                <div class="tohokuret btn_gray" id="tohokuret">0</div>
                                            </span>件キープ中<br>確認する</label></button>
                                </div>
                            </div>
                        </div>

                    </div>
                </ul>
            </div>


            <button class="trigger_keep_btn2 btn_gray" id="trigger_keep_btn">
                <label for="trigger_keep">
                    <span id="counter_dis" class="right_down_btn">
                        <div class="tohokuret btn_gray" id="tohokuret">0</div>
                    </span>件キープ中<br>確認する
                </label>
            </button>


            <!-- キープ一覧のモーダル -->
            <div class="modal_keep" id="modal_keep">
                <div class="modal_wrap">
                    <input id="trigger_keep" type="checkbox">
                    <div class="modal_overlay">
                        <label for="trigger_keep" class="modal_trigger"></label>
                        <div class="modal_content">
                            <label for="trigger_keep" class="close_button">✖️</label>
                            <!-- モーダルの中身 -->
                            <div class="modal_keep_header">
                                <h1 class="keep_view">キープ一覧</h1>
                            </div>
                            <btn class="keep_btn">
                                <div class="button05">
                                    <button class="bn632-hover bn19 keep_inquiry_btn" id="keep_inquiry_btn" type="submit" form="inquiry_submit" value="問い合わせる">
                                        <span id="count_dis">
                                            <div class="tohokuret" id="tohokuret2">0</div>
                                        </span>件キープ中<br>問い合わせる

                                    </button>
                                </div>
                            </btn>
                            <container class="filter keep_container" id="js-filter">
                                <div class="modal-filter-items">
                                    <ul class="in_modal_filter-items">
                                        <?php foreach ($listed_agents as $listed_agent) : ?>
                                            <li class="agent_box keep_agent_box" id="keep_agent_box_<?php echo $listed_agent['id'] ?>" style="display:none" data-filter-key="総合型">
                                                <img class="agent_img" src="img/insert_logo/<?php echo $listed_agent['insert_logo'] ?>" alt="企業ロゴ">
                                                <div class="agent_article">
                                                    <div class="agent_article_header">
                                                        <h1 class="agent_name"><?php echo $listed_agent['insert_company_name'] ?></h1>
                                                        <p class="num_company">取扱企業数：<?php echo $listed_agent['insert_handled_number'] ?></p>
                                                    </div>
                                                    <div class="agent_article_main">
                                                        <div class="agent_type">
                                                            <!--  タグ表示↓ -->
                                                            <?php foreach ($at_list as $agent_tags) : ?>
                                                                <?php if ($listed_agent['id'] === current($agent_tags)['agent_id']) : ?>
                                                                    <?php foreach ($agent_tags as $agent_tag) : ?>
                                                                        <p class="agent_tag">#<?= $agent_tag['tag_name']; ?></p>
                                                                    <?php endforeach; ?></td>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                            <!--  タグ表示↑ -->
                                                        </div>
                                                        <p class="recommend_points">特徴</p>
                                                        <div class="recommend_points_box modal_recommend_points_box">
                                                            <p><?php echo $listed_agent['insert_recommend_1'] ?></p>
                                                        </div>
                                                        <div class="recommend_points_box modal_recommend_points_box">
                                                            <p><?php echo $listed_agent['insert_recommend_2'] ?></p>
                                                        </div>
                                                        <div class="recommend_points_box modal_recommend_points_box">
                                                            <p><?php echo $listed_agent['insert_recommend_3'] ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="agent_article_footer modal_agent_article_footer">
                                                        <p class="span_published">掲載期間：<?php echo date("Y/m/d", strtotime($listed_agent['started_at'])); ?>〜<?php echo date("Y/m/d", strtotime($listed_agent['ended_at'])); ?></p>
                                                        <label onclick="buttonDelete(<?php echo $listed_agent['id'] ?>)" class="delete_btn" name=student_contacts[]>削除</label>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </container>
                            <!-- ここまでモーダルの中身 -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- ここまでキープ一覧のモーダル -->
        </container>
    </wrapper>

    <footer>
        <div class="inquiry">
            <p>
                craft運営 boozer株式会社事務局
                <br>TEL:080-3434-2435
                <br>Email:craft@boozer.com
            </p>
        </div>
    </footer>

    <script src="https://unpkg.com/scrollreveal@4.0.0/dist/scrollreveal.min.js"></script>
    <script src="main.js"></script>
    <script src="https://unpkg.com/scrollreveal"></script>
</body>

</html>