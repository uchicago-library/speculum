# Installing this site

Build a copy of this site, and start it in a docker container:

```console
git clone https://github.com/johnjung/speculum.git
docker build -t speculum .
docker run --rm -it -p 8080:8080 speculum
```
