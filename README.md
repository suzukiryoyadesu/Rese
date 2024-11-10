# Rese（リーズ）
飲食店予約サービス
![image](https://github.com/user-attachments/assets/302d8865-bf0b-4f4a-b774-dee93bf9bcde)

## 作成した目的
外部の飲食店予約サービスは手数料を取られるので自社で予約サービスを持つため。

## アプリケーションURL
http://localhost/

## 他のリポジトリ
なし

## 機能一覧
* ログイン機能
* 会員登録
* ログアウト
* 飲食店お気に入り追加
* 飲食店お気に入り削除
* 飲食店予約情報変更
* 飲食店予約情報追加
* 飲食店予約情報削除
* 検索
* レビュー投稿
* 店舗代表者登録
* 飲食店作成
* 飲食店更新
* 2段階認証
* メール送信
* リマインダー
* QRコードによる予約情報確認
* カード情報取得
* カード情報登録
* カード情報更新
* カード情報削除
* 決済

## 使用技術(実行環境)
* PHP 8.1.30
* Laravel 8.83.27
* mailhog
* javascript
* mysql 8.0.26
* phpmyadmin
* nginx 1.21.1
* Docker 25.0.3

## テーブル設計
![image](https://github.com/user-attachments/assets/0da505cb-3826-4cf8-99c5-e514b72e35b6)
![image](https://github.com/user-attachments/assets/2339d97b-ab1a-4794-812c-8b5dcf6b2d9b)
![image](https://github.com/user-attachments/assets/99e4cf3e-7607-41e8-b0de-a16da38d12c3)

## ER図
![rese](https://github.com/user-attachments/assets/ceb899d8-653c-471d-8dd3-69f49661a9ee)

## 環境構築
### コマンドライン上
```
$ git clone https://github.com/suzukiryoyadesu/Rese.git
```

```
$ docker-compose up -d
$ docker-compose exec php bash
```

### PHPコンテナ上
```
$ composer install
```

### src上
```
# .env.local(ローカル環境用)
$ cp .env.local .env
$ sudo chmod -R 777 storage
```

### PHPコンテナ上
```
$ php artisan key:generate
$ php artisan migrate --seed
```

```
$ php artisan storage:link
```

```
$ apt-get install cron
$ apt-get install vim
$ crontab -e
```

```
* * * * * cd /var/www && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

```
$ service cron start
```