document.addEventListener("DOMContentLoaded", function () {
    let inputHamburger = document.getElementById('hamburger__input');
     // ハンバーガーメニューボタンがクリックされたときのイベントハンドラを設定
    inputHamburger.addEventListener("click", function () {
        // body要素を取得
        var body = document.body;

        // 現在のbodyのスタイルを取得
        var bodyStyle = window.getComputedStyle(body);

        // bodyのoverflowスタイルがhiddenかどうかを確認
        if (bodyStyle.overflow === "hidden") {
        // もしoverflowがhiddenなら、bodyのスタイルを元に戻す
        body.style.height = "";
        body.style.overflow = "";
        } else {
        // そうでなければ、bodyにheight: 100%とoverflow: hiddenを設定し、スクロールを無効にする
        body.style.height = "100%";
        body.style.overflow = "hidden";
        }
    });
});