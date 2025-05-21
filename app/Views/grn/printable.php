
<popup id = "popup" style = "Display:none">
  <head>      
      <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
      <style>

          .cont, .layout { 
          padding: 0;
          margin: 0;
          outline: none;
          font-family: Roboto, Arial, sans-serif;
          font-size: 14px;
          color: #666;
          line-height: 22px;
          
        }

        .textline{
          padding: 0;
          margin: 0;
          outline: none;
          font-family: Roboto, Arial, sans-serif;
          font-size: 14px;
          color: #666;
          line-height: 22px;
        }

        .heading{
          padding: 5;
          margin: 5;
          outline: none;
          font-family: Roboto, Arial, sans-serif;
          font-size: 32px;
          line-height: 22px;
          color: #444;
        }
        
        .hr{
          outline: none;
          font-family: Roboto, Arial, sans-serif;
          font-size: 32px;
          line-height: 22px;
          color: #444;
        }
        
        .main-block {
          display: flex;
          justify-content: center;
          align-items: center;
          padding: 3px;
        }
        
        .stock-table  {
          border:2px solid #666;
          border-collapse: collapse;
          font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;         
          width: 500px;
          
        }

        .stock-table th{
          border:2px solid #666;
          padding: 12px;
          text-align: left;
          color: #666;          
        }

        .stock-table tr{          
          padding: 12px;
          text-align: left;
          color: #444;          
        }
        
        .stock-table tr:nth-child(even){background-color: #f2f2f2;}

        .stock-table tr:hover {background-color: #ddd;}
      }
      </style>
    </head>
    <body class = "cont">
      <div class="main-block">

        <form class = "layout">
        
          <h1 class = "heading">Goods Received Note</h1>
          <p class = "textline">Date Received:<label class = "textline" id = "print_date"></label></p>
          <br>  
          <p class = "textline">GRN ID:&nbsp;&nbsp;<label class = "textline" id = "print_grn_id"></label></p>        
          <p class = "textline">Hub Name:&nbsp;&nbsp;<label class = "textline" id = "print_hub_name"></label></p>
          <p class = "textline">Actionee:&nbsp;&nbsp;<label class = "textline" id = "print_actionee"></label></p>
          <p class = "textline">Cost:&nbsp;&nbsp;R<label class = "textline" id = "print_Tcost"></label></p>         
          <div class = "container">
            <hr>
            
            <h1 class = "heading">Stock Recived</h1> 
            <hr>      
            <br> 

            <table class="stock-table">
                    <thead>
                    <tr>
                        <th>EBQ CODE</th>
                        <th>Metric</th>
                        <th>Quantity</th>                        
                        <th>Total Cost</th>   
                    </tr>
                    </thead>
                    <tbody id = "table_body">                                                     
                             
                    </tbody>
            </table>
          </div>

        </form>   
      </div>
    </body>
</popup>