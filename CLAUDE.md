# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

架空の会社「株式会社コンドウソリューション」向けグループウェア(日本工学院 卒業研究、G班4名)。PHP + PDO/MySQL(DB名 `groupware`)+ ローカル Bootstrap 5.3.8。ビルド・テスト・リンタは無い。Apache(`C:\Apache24`)で配信し、ブラウザから `http://localhost/groupware-lab/login/index.php` を開いて動作確認する。

## 学習サポートの方針(最優先)

ユーザーは学生で、理解して自分で書けるようになることが目的。
- 正解コードをまるごと貼らない。手順と考え方を示し、ユーザーが書いたコードを見て直す点を順に伝える(ヒント→ユーザーが書く→レビュー)。
- 説明は具体的な値を入れたトレースで行う。
- 動作は確かめた範囲と推測を分けて書く。試していないことを断定しない。
- ファイル整理・資料作成などの雑務は、そのまま実行してよい。
- 作業の記録・知見は Obsidian Vault(`C:\bekura_valut\bekura_vault\Claude\`)に書く。プロジェクトのフォルダには記憶用ファイルを作らない。

## ディレクトリ構成と注意点

- `common/` — 共通部品。`auth_check.php`(未ログインならログイン画面へ)、`db.php`(PDO 接続 `$connection`)、`config.php`(接続情報)、`get_user_info.php`(`$loginUser` / `$employee_name`)、`header.php`、`approver.php`(承認可能な職位)、`ringi_status.php`(稟議ステータスの表示名)、`common.css`(`:root` の色変数と `btn-app-*` 等)。
- `login/` `home/` `workflow/` `ringi/` — 画面。画面ごとの CSS は各フォルダに置く。
- `yuukyu/`(有給休暇申請)— メンバーからバックエンドが上がってきた(2026-10-29)。`yuukyuu_common.php`(休暇種類・承認者の職位)、`yuukyuu_submit.php`(検証→`yuukyuu` と `yuukyuu_approvers` に保存)、`yuukyuu.php`(記入画面。PHP 部分のみ)。フロントは未完成で、`ringi/ringi.php` を参考に作ってバックエンドへ接続する。旧 `yuukyuu.html` は古い仮のコードなので参考にしない。
- `config.php` は `.gitignore` 対象(DB 接続情報)。内容を CLAUDE.md・コミット・ログに書かない。

## アーキテクチャ

### 共通の流れ
各ページは先頭で `require_once __DIR__ . '/../common/auth_check.php'`(`session_start()` を含む)を読み、DB を使うページは続けて `common/db.php` を読む。`$_SESSION['user_id']` がログイン判定。

### 稟議(ringi)のフロー
`ringi.php`(申請フォーム。CSRF トークンをセッションに発行)→ `ringi_submit.php`(POST のみ)→ `workflow/workflow_main.php`。承認側は `approval_list.php` → `approval_detail.php` → `approval_action.php`。

- 申請者が選んだ承認者の並び順 = 承認順。`ringi_approvers.step_order`(1始まり)に保存し、`ringi.current_step` が今の承認段階。
- `approval_action.php` はトランザクション内で `ringi` と `ringi_approvers` を `FOR UPDATE` で取得し、「ログイン者が現在の段階の承認者で、status が `waiting`」であることを確認してから更新する。最終段階なら `ringi.status = 'approved'`、そうでなければ `current_step` を +1。
- ステータス値: 稟議 `pending / approved / returned / rejected`、承認者 `waiting / approved`。差し戻し・却下は未実装(`approval_action.php` で「準備中」と返す)。
- 承認者になれる職位は `common/approver.php` の `APPROVER_POSITIONS`(課長・部長・副社長・社長)。申請時にサーバー側でも DB と照合する。

## コードの書き方(既存コードに合わせる)

- 日本語コメント。ページ冒頭や処理の区切りに「何のためか」を書く。
- POST 処理ページは、POST 以外は一覧/フォームへリダイレクト(URL 直打ち対策)→ CSRF トークンを `hash_equals` で照合 → 入力値の検証 → DB 処理、の順。
- PRG パターン。結果は `$_SESSION['flash_message']`、入力エラーは `$_SESSION['ringi_errors']` と `$_SESSION['ringi_old']`(入力保持)で渡し、`header('Location: ...')` + `exit`。
- SQL は必ずプレースホルダ(`prepare`)。DB/セッション由来の値を画面に出すときは `htmlspecialchars`(固定値は不要)。
- DB エラーは `error_log` に詳細を残し、画面には一般的なメッセージだけ出す。複数テーブルの更新は `beginTransaction` / `commit` / `rollBack`。
- デザインは Bootstrap + `common.css`。統一感・メッセージ・ステータスのバッジ・確認ダイアログは削らない。

## 進め方の決定事項

- 稟議を10月中に完成 → 有給・備品購入の申請へ。出退勤のフロントは別メンバーが担当。12/3 の中間発表までに申請系・掲示板・出退勤を進める。
- 要件定義は「作る直前に必要な分だけ」書く。
- 作業ログ: `Desktop\日本工学院\卒業研究\卒研_作業ログ.xlsx`(フォルダごとのタブに理解度を記録)。
