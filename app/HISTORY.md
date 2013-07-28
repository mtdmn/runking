Running Kingdom
=======

2013.7.28 at library
--------------------
* 画面遷移イメージを作成した。紙ベース。
* TODO: view mapで複数ユーザを色分けしながら表示するようにしたい。
	* そのためにはもう一人ぐらいユーザを追加。
	* 追加したけど、fitnessActivityをfetchできなくなっている。何故だ？runkeeperのアプリケーションの設定で、fitness activityを読み込む権限が必要だった。
* パスワード情報が平文で書かれているので、どっかのファイルにまとめてそのファイルをアップしないようにしよう。app/Lib/runkeeper_settings.php にまとめた。
* 既存のユーザがauthorizeしたときにNULLでuserテーブルにアカウントが追加されてしまうバグを発見したので、authorizeでerrorが帰ってきた時には何もしない処理を追加。
* phpmyadminのtimeoutを延長するconfigを入れた。
* repogitoryの初期化。

2013.7.13 at library
--------------------
* 完全に放置してしまったので、何をやっていたのか復習モード。
* Runkeeper.shellってなんぞ？
* appディレクトリで、cake runkeeper だけで実行できるっぽい。いじるしかない。
* dbへの登録はGPXファイルを持ってくることができれば、同じ処理が使い回せるが、どうもそんなAPIは無さそう。
	* GpxParserをwktとかgpxとかの種類を食わせるようなコンストラクタに変更した。
* Console/に置いたスクリプトを実行する場合、Controller/配下に置いたライブラリをincludeしようとすると、include_pathに無いと怒られる。app毎にinclude_pathを追加できればいいんだけど。
	* Console/cake.phpとwebroot/index.phpを編集して、include_pathにapp/Libを追加して、そこにGpxParser関係を置くようにした。
* DBに突っ込む処理完了。replaceintoを使うために、Model/User.phpでsqlべた書きしているので、フィールドが増えたらいちいちModelをいじらないといけない。
	* timestampの部分がちゃんと入ってないので、runkeeperのjsonのtimestampからmysqlの理解できるtimestampに変換しないといけない。"Tue, 1 Mar 2011 07:00:00",
	* Runpointテーブルのlatlngをprimary keyにして、replace intoをうまく使えるように変更。pointidは削除。
* workoutテーブルにも突っ込むようにした。既に同じactivityが登録されていればrunpointの登録をskipするようにした。
* RunpointテーブルにWorkout_IDを追加した。
* TODO: view mapで複数ユーザを色分けしながら表示するようにしたい。

2013.5.26 at library
--------------------
* icecoderをちょっと試してみたものの、パーミッションの設定は合っているはずなのに、エラーも出ず保存できないという問題、UTF-8のエンコードで書いた日本語を表示しようとすると、他のエンコードに変換されてしまって表示されない、という問題に遭遇してやめた。
codiadというのをそのうち試してみよう。
* まずはuser tableを作るところから。
	* usersを作成.
	* authorizeした時に、登録済みかどうかを確認して、未登録ならdbにinsertする。
* controllerの命名規則(単複)がおかしかったので修正。
	http://www.cpa-lab.com/tech2/inflects/
* さて次はactivityを持ってくる部分。
	* cronで実行する処理なので、別ファイルで実装しよう。
	* userテーブルから、rkidとtokenを拾ってきて、24時間以内のactivityを取得する。
	* 重複投入をさけるために、workoutの管理はやっぱり必要か。
	* workoutテーブルにrunkeeperのactivity idを入れるようにする。
	* runpointにもworkdout idを付けられるようにしよう。
* workout, userのindex()とindex.ctpを作成。
* console(cron)からの実行方法についてお勉強
	* sudo -u apache /usr/bin/php ../cake.php Runkeeper "" "" -app /var/www/arifumi_net/data/cakephp/app
	* consoleで実行して、usersを持ってきて、user毎にrunkeeperのactivityを持ってくるところまで実装。
	* Q. ところでこのAPIのrate limitとかはどうなってるんだろう？
* Runkeeper関係の処理をlibraryにまとめうとしたけど、そのファイルの置き場としていい場所がなく、include_pathを変更するのも大変そうだったので、やめた。 
* この自動処理が終われば、あとはユーザーインターフェースを作り込んだら、一通り実装完了かな。
	
2013.5.18 at library
--------------------
* 次はそろそろアカウントの話かな？
* UIデザインはtwitter bootstrapを使ってみよう
* runkeeper連携 http://developer.runkeeper.com/healthgraph
	* まずはAPIを眺めて当たりをつける
	* ユーザDBテーブルのスキーマ設計
	* cakephp用のoauthライブラリ http://code.42dh.com/oauth/
	* https://runkeeper.com/apps/authorize?client_id=fa85c607244c491f825f66e8dcf704ef&response_type=code&redirect_uri=http://vps.arifumi.net
	* 結局、ほとんどライブラリのお世話になることなく、tokenを取得できた。でもこれって、ちゃんとOAuth実装できてんのかな。
* jsonフォーマットでpathが降ってくるので、これをWKTとかに変換してあげないといけない。
	* GpxParserクラスのコンストラクタでGPXとWKT(LINESTRING)を選択できるようにしないといけない。
	* https://api.runkeeper.com//fitnessActivities/[activity_id]?access_token=xxx
* ユーザーアカウントに関する処理
	* 最初にrunkeeper連携が終わった時の処理。
	* DBにアカウントを作成する。useridはrunkeeperと同じでいい気がする。
	* いや、やっぱりuseridはsequencialにintegerで付けて、RK連携のユーザにはRKIDのフィールドを埋めるようにしよう。
	* 名前とかは連携させた時に取ってきたい。

2013.5.11 at library
--------------------
* 早く、自分の走行データをがんがん突っ込めるようにしたい
* そのためには、あとはJavascriptからkingdomのデータを取得するようにする部分ができていない。
	* AJAXでやるなら、緯度経度を指定して、その周辺のbox情報を返すようなcgiをサーバに設置することになる？あとユーザIDも？
	* http://docs.openlayers.org/library/request.html
	* OpenLayers.Request メソッドなる便利なものがあるらしい
* とりあえず最初はajaxじゃなくて、最初に自分のkingdomのデータを全部取得するようにすればいいか？
	* それはそうだけどなんかかっこ悪いのぉ
	* OpenLayers用のLayoutを追加してmetaタグ内でcssとjsを読み込むようにした。
	* View/Runpoints/map.ctpにol.htmlのscriptコードを移植。bodyタグのonloadは書くのが面倒そうだったので、document.body.onloadを使うことにした。
	* DBから持ってきたPOINTのWKTをgeophpでデコードしようとしたが、なんだか処理が重たいようだったので、正規表現でparseするようにした。
	* parseしたものをjavascriptのboxとしてべた書きしてとりあえず表示するところまでできた。あれでもなんか表示がおかしい。
	* どうやらDBに入っているデータがおかしくて、中抜きになってしまっている。
	* GpxParser.phpのclass化したときに、最後にreturnする部分の処理が間違っていたので修正。
* 同じ位置のエントリーが複数DBに入っちゃってる予感。
	* どうやって重複エントリーを防ぐか。
	* 重複エントリーはするけど、有効なものだけにフラグを付けるようにするとか。
	* UNIQUEをpoint型に追加してみた。INSERTしてdup keyでerrorが発生すると止まってしまう。
	* INSERTする時に、IGNOREしたりREPLACEにしたり色々とできるらしい。REPLACEにする方向で。CakephpからREPLACEにするいい方法が無いっぽい。
	* Modelのsaveを使わずに専用のREPLACE INTOクエリーを実行するメソッドを追加して解決。
* 特定の経度の時にpointが無くなる問題が発生。GpxParser.phpのバグっぽい。
	* invert ringのバグでした。

2013.5.1 at ICU
--------------------
* ベースレイヤー画像選び
	* http://mc.bbbike.org/mc/?num=2&mt0=mapnik&mt1=mapnik-bw
	* これのEsri Topoっていうのがいい感じである。 
	* というか、OSM mapnikに他のレイヤーをかぶせて色を薄くすればなんとでもなる。
* OSMで、lonlatを使いこなせるようになろう。
	* transformでlonlatから変換すればいいだけっぽい。boxesが増えてくれば処理が重たくなるかもしれないけど、とりあえずこれでいいか。
	* 50x50個のboxで負荷テストしてみたけど、ブラウザの負荷としては大したことはないっぽい on Firefox
	* git-config edited.

2013.4.30
--------------------
* git ignoreの設定。
  $ echo "*.swp" >> ~/.gitignore_global  # ファイル名は好きなもので OK
  $ echo ".DS_Store" >> ~/.gitignore_global
  $ git config --global core.excludesfile ~/.gitignore_global
* WMSじゃなくてOpenStreetMapをベース画像として使ってみた。
  * OpenLayers.Layer.OSM()
  * しかし、ケバい色使いで、全然重ねられそうにないんだけど。google mapの色使いはやっぱり秀逸だなぁ。

2013.4.27
--------------------
* GW中になんとか形にするぞー！
* え、OpenLayersはlonlatなEPSG:4326な表現手法は基本的に使わないらしい。EPSG:900913とかいうのを使え、と。変換はできるらしい。
	* でも、サンプルコード見たら、めっちゃEPSG:4326で書かれてるっぽいが。
* ちょっとHTMLいじくってたらDOCTYPEでハマった。互換モードでしか動かないコードを書いてしまったのが原因らしい。面倒なので互換モードにしとこう。
	* http://tech.bayashi.jp/archives/entry/techweb/2009/002506.html
	* http://www.dspt.net/html_tag/mode.html
* ということでsmallmapから脱却できた。
* setCenterを覚えた。ついでにzoomも覚えた。
* WMSの地図だけじゃ殺風景なので、もうちょっといい感じのものに変えたい。
* 座標データはajaxでサーバから拾ってくる感じかな。

2013.4.22
--------------------
* 自分のkingdom表示がその次のステップ。
* PHPのframeworkも色々有るらしいけど、cakephpで良かったのかな？
* 地図について
	* どうやらgoogle maps apiは有料化してしまったので、使わない方が良さそう。openstreetmapsを使った方が良さそう。しかしこれ、地図を作るのは集合知でやっているのはいいとして、トラフィックが増えてきても大丈夫なのか？
	* OSM + OpenLayers APIでやってみよう。
	* こんな感じでいいんじゃね？ http://openlayers.org/dev/examples/boxes-vector.html
	* OpenLayersのboundsとかその辺を理解しないと使えないぞ。
* ところで、spモードでテザリングしてるときにsshが切れないようにする方法は無いの？
	* ServerAliveInterval 60 をssh_configに追加した。

2013.4.20
--------------------
* 今日の目標はGPXファイルをアップロードして、それをDBに突っ込むところまで。
	* 以前作っていたgeoPHPを使ったスクリプト、mytest.phpをGpxParserクラスとして仕立て直した。
	* 実際にcakephpから呼び出してみるとどうもloadの当たりでエラーになる。CLIでこのクラスを使うスクリプトでデバッグする。
	* get_file_contents()は生成したclassのinstanceの先で実行した場合と、元で実行するのとで、意味が違うらしい。
	* ようやく、cakephpでpointsのarrayを取得するところまでできた。
	* geoPHPのライブラリはcakephp/app/Controller/geoPHP以下に置くことにした。
	* これをDBに突っ込む処理だん。いくつかファイルをアップしてみたが特にエラーは発生していない。というか、エラー処理全然やってないな。

2013.4.14
--------------------
* git-credential cacheを設定した。こりゃ便利だ。
* POINTの扱いについて
	* 表示するとき: Runpoint.phpでvirtualFieldsを設定して、latlngをAsTextで表示。
	* 保存するとき:
	* POINT(35.100, 139.200)というデータを保存する方法。
	* PointFromText('POINT (35.691147 139.702084)') というSQLにすればいいはず。
	* これをModelでやるかControllerでやるか？表示はModelでやったから、同じように保存もModelでやりたい。
	* Controllerでやってみたところ、PointFromText()関数がクオーテーションでくくられてしまうため、ちゃんとinsertできないことがわかった。ということで、Modelでやるしかないっぽい。
	* Modelでどうやるかを調べてもすぐにはわからず。結局、dbosource::expressionを使うといのをここで発見した。 http://stackoverflow.com/questions/5864879/how-to-use-mysql-now-function-in-cakephp-for-date-fields
	* 無駄に時間を使ってしまった。
	* とりあえず、Modelのset()とsave($data)をoverrideして実装した。
* 次の処理はファイルアップロード->スクリプトで前処理->DB投入まで。
	* upload.ctpを作って、ファイルアップロードページを作成しようとしているところで時間切れ。


2013.3.26
--------------------
* cakephpがpearのchannelを入れてもインストールできないので、go-pearを使ってpearのインストールやり直し

 1. Installation base ($prefix)                   : /usr
 2. Temporary directory for processing            : /tmp/pear/install
 3. Temporary directory for downloads             : /tmp/pear/install
 4. Binaries directory                            : /usr/bin
 5. PHP code directory ($php_dir)                 : /usr/share/pear
 6. Documentation directory                       : /usr/share/pear/docs
 7. Data directory                                : /usr/share/pear/data
 8. User-modifiable configuration files directory : /usr/share/pear/cfg
 9. Public Web Files directory                    : /usr/share/pear/www
10. Tests directory                               : /usr/share/pear/tests
11. Name of configuration file                    : /etc/pear.conf

* pear channelを追加して、cakephpをpearからインストール。ライブラリだけが入ったらしい。
* cakephp/index.phpのCAKE_CORE_INCLUDE_PATHをdefineしないように変更したらphp.iniのinclude_pathを見に行くようになった。
* php.iniからmysqlのuser,passwordの記述を削除する。
* なに、pdo_mysqlが必要だって？
	* なんか、php.iniで指定しないといけないらしいけど、困ってからにしよう。
	* 無くても動いてるぞ

* アソシエーションについて勉強した。多分使うはず。
	* http://qiita.com/items/c655abcaeee02ea59695

