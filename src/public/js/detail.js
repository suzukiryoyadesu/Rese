const formInputDate = document.forms.reservation__form.date; //document.formsでformの中身を取得,さらに後ろの記述で細かく指定できる

window.addEventListener('load',()=>{     //ウェブ上のあらゆるオブジェクトの読み込みがすべて完了したら
    let inputDate = document.getElementById('input_date');//input_dateのidを検索して
    inputDate.textContent = formInputDate.value  //inputDateのtextcontentにformInputDateのvalueをくっつける
})


formInputDate.addEventListener('input',()=>{     //formInputDateにinputがされたら
    let inputDate = document.getElementById('input_date');
    inputDate.textContent = formInputDate.value
})

const formInputTime = document.forms.reservation__form.time;

window.addEventListener('load',()=>{
    let inputTime  = document.getElementById('input_time');
    inputTime.textContent = formInputTime.value
})

formInputTime.addEventListener('input',()=>{
    let inputTime  = document.getElementById('input_time');
    inputTime.textContent = formInputTime.value
})

const formInputNumber = document.forms.reservation__form.number;

window.addEventListener('load',()=>{
    let inputNumber  = document.getElementById('input_number');
    inputNumber.textContent = formInputNumber.value + "人"
})

formInputNumber.addEventListener('input',()=>{
    let inputNumber  = document.getElementById('input_number');
    inputNumber.textContent = formInputNumber.value + "人"
})

const formInputPayment = document.forms.reservation__form.payment_id;

window.addEventListener('load',()=>{
    let inputPayment  = document.getElementById('input_payment');
    inputPayment.textContent = formInputPayment.options[formInputPayment.selectedIndex].textContent
})

formInputPayment.addEventListener('input',()=>{
    let inputPayment  = document.getElementById('input_payment');
    inputPayment.textContent = formInputPayment.options[formInputPayment.selectedIndex].textContent
})