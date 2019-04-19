<div id="basicsearch">
<p>basic | <a href="#" id="advancedsearchlink">advanced search</a></p>
<p><b>Search for text:</b></p>
<form>
<p style="text-align: center;">
<input type="text" name="search[0]" size="25"/> in
<select name="searchnode[0]">
<option value="all">entire record</option>
<option value="agent">agent</option>
<option value="number">Chicago number</option>
<option value="city">city</option>
<option value="date">date</option>
<option value="inscription">inscription</option>
<option value="printmaker">printmaker</option>
<option value="publisher">publisher</option>
<option value="subject">subject</option>
<option value="title">title</option>
</select>
&nbsp;
<input type="submit" value="submit"/>
</p>
</form>
</div>

<div id="advancedsearch">
<p><a href="#" id="basicsearchlink">basic</a> | advanced search</p>
<form>
<table style="margin: 0 auto;">
<tr>
<td colspan="2"><p><b>Search for text:</b></p></td>
</tr>
<tr>
<td/>
<td>
<p>
<input type="hidden" name="searchboolean[0]" value="or"/>
<input type="text" name="search[0]" size="35"/> in
<select name="searchnode[0]">
<option value="all">entire record</option>
<option value="agent">agent</option>
<option value="number">Chicago number</option>
<option value="city">city</option>
<option value="date">date</option>
<option value="inscription">inscription</option>
<option value="printmaker">printmaker</option>
<option value="publisher">publisher</option>
<option value="subject">subject</option>
<option value="title">title</option>
</select>
</p>
</td>
</tr>

<tr>
<td>
<select name="searchboolean[1]">
<option value="and">and</option>
<option value="or">or</option>
</select>
</td>
<td>
<p>
<input type="text" name="search[1]" size="35"/> in
<select name="searchnode[1]">
<option value="all">entire record</option>
<option value="agent">agent</option>
<option value="number">Chicago number</option>
<option value="city">city</option>
<option value="date">date</option>
<option value="inscription">inscription</option>
<option value="printmaker">printmaker</option>
<option value="publisher">publisher</option>
<option value="subject">subject</option>
<option value="title">title</option>
</select>
</p>
</td>
</tr>

<tr>
<td>
<select name="searchboolean[2]">
<option value="and">and</option>
<option value="or">or</option>
</select>
</td>
<td>
<p>
<input type="text" name="search[2]" size="35"/> in
<select name="searchnode[2]">
<option value="all">entire record</option>
<option value="agent">agent</option>
<option value="number">Chicago number</option>
<option value="city">city</option>
<option value="date">date</option>
<option value="inscription">inscription</option>
<option value="printmaker">printmaker</option>
<option value="publisher">publisher</option>
<option value="subject">subject</option>
<option value="title">title</option>
</select>
</p>
</td>
</tr>

<tr>
<td/>
<td style="text-align: right;"> 
<br style="height: 1em;"/>
<input type="submit" value="submit"/>
</td>
</tr>
</table>
</form>
</div>
