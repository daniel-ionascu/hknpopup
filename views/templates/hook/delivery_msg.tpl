<style>

    .th_allpage {
        z-index: 9998;
        position: fixed;
        width: 100%;
        height: 100%;
    }

    .th_delivery_msg {
        display:flex;
        flex-direction:column;
        align-items: center;
        justify-content: space-around;
        position: fixed;
        z-index: 9999;
        background-color: rgba(40, 40, 40, 0.9);
        width: 100%;
        height: 25%;
        font-size: 20px;
        color: white;
        text-align: center;
        bottom: 0;
        padding:10px 0;
    }
    .th_button_ok{
        font-weight:700;
        background-color:#DC2A0B;
    }
    .th_button_ok:hover{
        color:#DC2A0B;
        background-color:white;
    }
    .th_delivery_hidden{
        font-weight:700;
        transition: color 0.5s, text-shadow 0.5s;
    }

</style>

<div class="th_allpage hidden">
    <div class="th_delivery_msg" style="z-index:9999">
        <span style="font-size:22px">Stimate Client,</span>
        {$message_all nofilter}
        <span class="th_delivery_hidden hidden">Va rugam acceptati pentru a continua</span>
        <button class="btn btn-primary th_button_ok">AM INTELES</button>
    </div>
</div>

