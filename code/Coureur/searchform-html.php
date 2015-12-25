 <div id='search'>

    <form name="searchform" id="searchform" action="" method="post">

        <fieldset>
            <legend >
                <span id='search_legend' class="msg"></span>
                <span class='help wide'>
                    <span id="searchform_help" class="wide msg"></span>
                </span>
            </legend>



            <input name="lang" type="hidden" id="search_input_lang" value="fr"/>
            <label class="left">
                <span id='l_search_lic' class="msg"></span>                   
            </label>
            <input name="search_lic" id="search_lic" type="text"/>
  <!--          <input name="search_submit" type='submit' value="Chercher">
            -->          


            <label class="left"><span class="msg" id='l_search_isaf'></span></label>
            <input name="search_isaf"
                   id="search_isaf" type="text" />

            <input name="search_submit" type='submit' value="Chercher">


            <div class="caveat" style="padding:5px;padding-left:0px">
                <span id="searchform_caveat" class="msg"></span>
            </div>


        </fieldset>

    </form>

</div><!-- recherche par licence ou numero ISAF -->
