Running Kingdom
=======

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

