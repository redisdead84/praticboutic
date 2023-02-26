<!DOCTYPE html>
<html>
  <head>
    <title>Initialisation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
    <script type="text/javascript">
      var bouticid;
      var logo;
      var nom;
      var abo;
      
      async function initSession(customer, method, table)
      {
        var objboutic = { requete: "initSession", customer: customer, method: method, table: table};
        const response = await fetch('frontquery.php', {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body:JSON.stringify(objboutic)
        });
        if (!response.ok) {
          throw new Error(`Error! status: ${response.status}`);
        }
        await response.json();
      }

      
      async function getBouticInfo(customer)
      {
        var objboutic = { requete: "getBouticInfo", customer: customer};
        const response = await fetch('frontquery.php', {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body:JSON.stringify(objboutic)
        });
        if (!response.ok) {
          throw new Error(`Error! status: ${response.status}`);
        }
        const data = await response.json();
        bouticid = data[0];
        logo = data[1];
        nom = data[2];
      }
      
      async function getAboActif(bouticid)
      {
        var objboutic = { requete: "aboactif", bouticid: bouticid};
        const response = await fetch('frontquery.php', {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body:JSON.stringify(objboutic)
        });
        if (!response.ok) {
          throw new Error(`Error! status: ${response.status}`);
        }
        const data = await response.json();
        abo = data;
      }
      
      window.onload = async function()
      {
        var bakbarre = sessionStorage.getItem("barre");
        sessionStorage.clear();
        if (bakbarre == "close")
          sessionStorage.setItem("barre", "close");
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const customer = urlParams.get('customer');
        if (!customer)
          window.location = "error.php?code=nocustomer";
        else 
        {
          const method = urlParams.get('method') ? urlParams.get('method') : '3';
          const table = urlParams.get('table') ? urlParams.get('table') : '0';
          await initSession(customer, method, table);
          await getBouticInfo(customer);
          if (!bouticid)
            window.location = "error.php?code=nobouticid";
          else 
          {
            await getAboActif(bouticid);
            if (abo.length == 0)
              document.location.href = "error.php?code=noabo";
            else
              document.location.href = 'carte.php';
          }
        }
      }
    </script>
  </body>
</html>

