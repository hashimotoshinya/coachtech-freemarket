# 🛍 Laravel フリマアプリ

このアプリはフリーマーケットサービスです。  
ユーザーは商品を出品・購入・コメント・いいね（お気に入り）などが可能です。
---
## ✅ 主な機能

- ユーザー登録・ログイン（メール認証あり）
- 商品の出品・購入
- 商品一覧・検索
- 商品へのコメント投稿
- いいね（お気に入り）機能・マイリストへの登録
- Stripe決済（カード・コンビニ）
- 購入履歴・出品履歴の確認
- プロフィールの編集

---

## 🛠 環境構築手順

### 1. 前提環境

- PHP 8.1+
- Composer
- Laravel 10+
- Node.js
- MySQL / SQLite（開発用）
- Docker（MailHog使用時）
- Stripeアカウント（テストキー）

### 2. セットアップ手順

```bash
# リポジトリをクローン
git clone https://github.com/your-name/freemarket-app.git

cd freemarket-app

# 環境ファイルをコピー
cp .env.example .env

# 依存関係をインストール
composer install
npm install && npm run dev

# アプリケーションキーを生成
php artisan key:generate

# データベースをマイグレート & シーディング
php artisan migrate --seed

# 開発サーバーを起動
php artisan serve
```
---

## 📸 ER 図

データベース設計の概要：

![ER図](docs/er-diagram.png)

