<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Price</title>
</head>
<body>
    
    <center>
    <div class="parent">
        <div class="container">
            <p>Latest Price from CoinMarketCap</p>
            <p>Connection status: <strong>Connection established</strong></p>
            <p>Latest price BTC: <span id="current-price"></span> </p>
            <br>
            <br>
            <br>
            <div id="old-prices">
                
            </div>
        </div>
    </div>
</center>


    <script>
            let currentPriceEl = document.querySelector('#current-price')
            let currentPrice = 0;
            let oldPrices = document.querySelector('#old-prices')
            async function getPrice(){
                let res = await fetch('/price');
                let price = (await res.json())['price']
                return price;
            }
            getPrice().then(p => {
                currentPriceEl.textContent = `$${p}`
                currentPrice = p;
            });
            setInterval(async () => {
                let oldPrice = currentPrice;
                let price = await getPrice();
                currentPrice = price;
                currentPriceEl.textContent = `$${price}`

                let el = document.createElement('p')
                el.textContent = `$${oldPrice}`

                oldPrices.appendChild(el)
                
            }, 60000)
    </script>
    <style>
        .parent {
            margin-top: 10%;
        }

    </style>
</body>
</html>