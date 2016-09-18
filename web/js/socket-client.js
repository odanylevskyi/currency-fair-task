/**
 * Created by oleksii on 17/09/2016.
 */
$( document ).ready(function() {
    $('#vmap').vectorMap({
        map: 'world_en',
        enableZoom: false,
        pins: {
        },
        pinMode: 'content',
    });

    var socket = io.connect('http://ec2-52-43-235-109.us-west-2.compute.amazonaws.com:8890/');
    var counter = 0;
    socket.on('notification', function (data) {
        var message = JSON.parse(data);
        counter = $('.messages > table > tbody > tr').length;
        if (counter == 0) {
            $('.messages > table > tbody > tr').remove();
        }
        if(counter > 18) {
            $('.messages > table > tbody > tr').last().remove();
        }
        counter++;
        var html = '<tr><td>'+message.userId+'</td><td>'+message.originatingCountry+'</td><td>'+message.currencyFrom+'/'+message.currencyTo+'</td><td>'+message.amountSell+'/'+message.amountBuy+'</td><td>'+parseFloat(message.rate).toFixed(2)+'</td><td>'+message.timePlaced+'</td></tr>';
        $('.messages > table > tbody').prepend(html);
        var pins = new Object();
        pins[message.originatingCountry.toLowerCase()] = message.originatingCountry;
        jQuery('#vmap').vectorMap('placePins', pins, 'content');
        setTimeout(function(){
            jQuery('#vmap').vectorMap('removePin', message.originatingCountry.toLowerCase());
        }, 1000);
    });
});