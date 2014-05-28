		<div id="search">
			<form action="/search/" method="GET">
				<input type="text" name="term" <?php if (isset($_GET["term"])) echo "value=\"".$_GET["term"]."\""; ?>/>
				<button type="submit" class="btn btn-large"><i class="icon-search"></i></button>
			</form>
		</div>
