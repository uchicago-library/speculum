# script to rebuild indexes for the speculum project.
# this gets an input file from cdbmake over http. 

for index in agent all city date engraver huelsen inscription number printmaker publisher subject title
	do 
		echo building ${index} index
		wget -q -O - http://speculum.lib.uchicago.edu/buildindexes.php?index=${index} | cdbmake web/indexes/${index}.cdb web/indexes/${index}tmp.cdb
		echo building ${index} text lookup
		wget -q -O - http://speculum.lib.uchicago.edu/buildtextlookup.php?index=${index} > web/indexes/${index}.text
done

for index in city collection date engraver huelsen number printmaker publisher subject 
	do 
		echo building ${index} browse
		wget -q -O - http://speculum.lib.uchicago.edu/buildbrowses.php?browse=${index} > web/browses/${index}.browse.xml
done

