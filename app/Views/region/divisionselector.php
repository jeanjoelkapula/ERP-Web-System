<div class = "form-group">
    <label class="col form-label">Division</label>
        <div class = "col">
            <select class="custom-select btn-block" name = "division_var">


                <?php 
                   
                 $sql = "select DIVISION_NAME from TBL_DIVISION;";
                           
                 $query = $db->query($sql);
                 foreach ($query->getResult() as $row): {
                    
                    echo '<option ';

                    if(isset($division_name)){
                        
                        if(strcmp($row->DIVISION_NAME, trim($division_name, "'"))==0){

                            echo 'selected';

                        }

                    }

                    echo '>';
                    

                    echo $row->DIVISION_NAME.'</option>';

                } endforeach;
                                   
                ?>
               

            </select>
    </div>
</div>