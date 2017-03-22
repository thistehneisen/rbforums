<section class="container px2 py3">
    <h2 class="olive clearfix">Eksportēt datus CSV formātā</h2>
    <div class="flex flex-wrap">
        <div class="p1">
	        <form action="" method="post">
		        <label for="delimiter" class="inline-block mb2">Atdalītājsimbols: </label>
                <input type="text" name="delimiter" id="delimiter" value="," class="field col-1 mb2"><br>
		        <label for="type">Forma: </label>
		        <select name="type" id="type" class="field">
			        <option value="day1">Day 1</option>
			        <option value="day2">Day 2</option>
			        <option value="media">Media</option>
		        </select><br><br>
		        <button type="submit" class="block btn btn-primary bg-navy col-12">Eksportēt</button>
	        </form>
        </div>
    </div>

</section>