<html>
    <head>
        <title>HTML Element Counter</title>
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <div id="main">
<div class="form">
    <h2>HTML Element Counter</h2>
    <form id="searchForm">
    <label>URL<input type="url" name="url" id="url" required></label>
    <label>Element<input type="text" name="element" id="element" required></label>
    <label><button class="button" type="submit">Submit</button></label>
    </form>
    <div id="loading">
        <img src="images/loading.gif">
    </div>
    <div id="result">
        <h3 id="resulthead">Result</h3>
        <label id="r_url">Url: <span></span></label>
        <label id="r_status">Status: <span></span></label>
        <label id="r_element">Element: <span></span></label>
        <label id="r_count">Number detected: <span></span></label>
    </div>
    <a class="statistics" href="url_stat.php">View Statistics</a>
</div>
        </div>
    </body>
    <script>
        function showLoading(show){
            const loading = document.getElementById("loading");
            const result = document.getElementById("result");
            switch(show)
            {
                case 1:
                loading.style.display = "block";
                result.style.display = "none";
                break;
                case 0:
                loading.style.display = "none";
                result.style.display = "block";
                break;
            }
        }
        function setResponse(url,response){
            console.log(response);
            document.getElementById("r_url").querySelector("span").textContent =url;
            document.getElementById("r_status").querySelector("span").textContent =response['message'];
            document.getElementById("r_element").querySelector("span").textContent =response['element'];
            document.getElementById("r_count").querySelector("span").textContent =response['length'];
        }
        const form = document.getElementById("searchForm");
        form.addEventListener("submit",async function(e){
            showLoading(1);
            e.preventDefault();
                try {
                    const url = form.querySelector("#url");
                    const element = form.querySelector("#element");
                    const response = await fetch("requestHandler.php",{ 
                            method: "POST",
                            // Adding headers to the request
                            headers: {
                                "Content-type": "application/json"
                        },
                            // Adding body or contents to send
                            body: JSON.stringify({
                                url: url.value,
                                element:element.value
                        })
                        });
                    //console.log('response.status: ', response.status); // 👉️ 200
                    if(response.status===200)
                    {
                    const rep = await response.json();
                    setResponse(url.value, rep);
                    }
                    else if(response.status>=400){
                        const rep = await response.json();
                        setResponse(url.value, rep);
                    }
                } catch (err) {
                    console.log(err);
                }
               showLoading(0);
        })
    </script>
    </html>