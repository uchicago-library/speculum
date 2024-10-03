# Speculum Romanae Magnificentia Website

This is a legacy website for the Speculum Romanae Magnificentia. 

Metadata for this website represents a lot of careful work from Rebecca Zorach, formerly of 
the University's Art History department, and graduate students working with her on this
project. The data is in [VRA](https://www.loc.gov/standards/vracore/) format, and a series of [CDB](https://www.postfix.org/CDB_README.html) indexes allow for searching and browsing. Metadata for the project has been stable for years, and the .cdb files should be considered
fixed at this point- rebuilding them from the original data may not be possible. 

The site originally used Flash-based components for the itineraries feature and for zoomable
viewing, but it has recently been overhauled to use IIIF and the Universal Viewer. For
a list of all ARK identifiers and digital objects in the project, navigate to the ark_data.db
database and run the following command:

```console
SELECT * FROM arks WHERE project='speculum';
```

## Running the site locally using Docker

Build a copy of this site, and start it in a docker container:

```console
git clone https://github.com/johnjung/speculum.git
docker build -t speculum .
docker run --rm -it -p 80:80 speculum
```
