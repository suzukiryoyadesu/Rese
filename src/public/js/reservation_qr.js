const video = document.createElement('video');
const canvasElement = document.getElementById('canvas');
const canvas = canvasElement.getContext('2d');
const loading = document.getElementById('loading');
const output = document.getElementById('output');
const btnRestart = document.getElementById('restartBtn');

window.addEventListener('load',()=>{
    startQR();
})

const startQR = () => {
    navigator.mediaDevices.getUserMedia({
        video: {
            audio: false,
            facingMode: 'environment'//'user' でインカメを使う
        }
    }).then((stream) => {
        video.srcObject = stream;
        video.setAttribute('playsinline', true);
        video.play();
        requestAnimationFrame(tick);
    }).catch(() => {
        loading.innerHTML = 'カメラを起動できませんでした';
    })
};


//QRの解析
function tick() {
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loading.hidden = true;
        canvasElement.hidden = false;
        restartBtn.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        //CanvasのgetImageDataメソッドで指定範囲のImageDataオブジェクトを取得する
        const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        //jsQRのメソッド
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert',
        });

        //QRが読み込めた時の挙動
        if (code) {
            drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
            drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
            drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
            drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
            outputData.href = '/reservation/confirm?reservation_id=' + code.data;
            //videoをcanvasに
            video.style.display = 'none';
            video.pause();
            output.hidden = false;
        } else {
            output.hidden = true;
        }
    }
    requestAnimationFrame(tick);
};


//QRを囲むライン
const drawLine = (begin, end, color) => {
    canvas.beginPath();
    canvas.moveTo(begin.x, begin.y);
    canvas.lineTo(end.x, end.y);
    canvas.lineWidth = 4;
    canvas.strokeStyle = color;
    canvas.stroke();
};

btnRestart.addEventListener('click', () => {
    startQR();
});
