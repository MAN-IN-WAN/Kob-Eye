<div class="row">
    <div class="col-md-6">
        <h2>Lettre d'informations</h2>
    </div>
    <div class="col-md-6">
        <form action="/[!Lien!]" method="post" style="float: right; margin-top: 22px;">
            <p>

                <input type="text" name="email" size="18"
                       value="your e-mail"
                       onfocus="javascript:if(this.value=='votre e-mail')this.value='';"
                       onblur="javascript:if(this.value=='')this.value='votre e-mail';"
                       class="inputNew" />
                <!--<select name="action">
                <option value="0">Subscribe</option>
                <option value="1">Unsubscribe</option>
                </select>-->
                <input type="submit" value="Valider" class="button_mini" name="submitNewsletter" />
                <input type="hidden" name="action" value="0" />
            </p>
        </form>
    </div>
</div>
