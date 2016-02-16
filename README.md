# Autocomplete project

The objective of this project is to build a simple autocomplete feature based on data extracted from wikipedia.  
See the [on-line demo](http://autocomplete.linkeddata.center/).

The html/javascript part is based on [jQueryUI autocomplete](http://jqueryui.com/autocomplete/), the server script is based on [BOTK](https://github.com/linkeddatacenter/BOTK-core) library.
Wikipedia knowledge is distilled by [Dbpedia](http://dbpedia.org) and hosted by [LinkedData.Center](http://linkeddata.center/).


## Background

Because is not always easy to understand the power of SPARQL and of Semantic Web technologies in day-by-day programming, I provided a simple example that solves a 
very general and frequent problem: autocomplete an input field selecting data from a large dataset.

Suppose that you want use jQueryUi autocomplete feature allowing the user to select one river in the world, and suppose that you want this list available
in different languages. You got a big problem: to populate and maintain the big data set needed by the autocomplete script.
 
Here is where the Semantic Web does the magic: you can use Dbpedia to access the full "Wisdom of the crowd" contained in Wikipedia and use it
	to get a list of all rivers, translated in any language!

Dpedia is a great public service but unfortunately it does't ensure SLA, 
	sometime the service is down for maintenance and you can't predict when this happens.
	This is not acceptable if you need to build a solid application based directly on such service.

A reasonable solution is to copy the data you need from dbpedia to your own knowlege base system, so you can safely use it in your application. 

This is where LinkedData.Center service plays its role. It allows you to quickly create and host a knowledge base populated 
from linked open data sources, from private data or from any combination of both. LinkedData.Centers exposes a dedicated and password protected sparql 
end-point full compliant with the last W3C semantic web standards. You can create data mashup, apply rules, 
data inferences and many other features. Last but not least LinkedData.Center keeps aligned your knowledge 
base with the data sources reindexing when needed.

## How it works

This project is composed by javascript/html page, a server script and a knowledge base configuration.

The  script, by default, connects to  https://hub1.linkeddata.center/demo/sparql endpoint. 
You can use your own LinkedData.Center instance ([free tiers available](http://linkeddata.center/home/pricing#cta)) just changing credentials in the api code.


The *demo* knoledge base is populated starting from a 
Knowledge Exchange Engine Schema (KEES) file (find it in pub/kees.ttl). This file is the core of the project. 
If you want to use  your own knowledge base instance, you need to include this file in the graph-db configuration like as in the [demo knowledge base](http://hub1.linkeddata.center/demo/cpanel/config).

In production environment do not link the master branch, instead use the preferred tagged version: e.g `[] kees:includes <http://linkeddata.center/project/autocomplete/1.0.0/pub/kees.ttl> .`

For more information about how to populate a knowledge base, please refer to [LinkedData.Center Knowledge base configuration handbook](http://linkeddata.center/help/devop/kees-profile). 

## Test in a local environment using Vagrant (suggested)

These instructions allow you to install and test the project on your local workstation using some simple virtualization technologies:

- install [vagrant](https://docs.vagrantup.com/v2/installation/) and [virtual box](https://www.virtualbox.org/) on your workstation.
- clone this project in directory of your workstation and cwd in it
- open a shell and type the command `vagrant up`. A new virtual machine with all needed tools will be ready and running in few minutes.
- point your browser to http://localhosts:8080/demo .
- to destroy your virtual host just type `vagrant destroy`

You should get locally the same results available in [E-Artspace demo site](http://autocomplete.linkeddata.center/).
 
## Install on your PHP web server

   - Publish the project in a web server that supports php 5 (with curl extension ).

The provision script contained in the Vagrant file will give an idea of a complete api installation on a ubuntu 14.04 box.

##The server side script
[jQueryUi remote autocomplete] (http://jqueryui.com/autocomplete#remote) requires a 
server script file. 
The script source that searches labels in wikipedia is provided in pub/label/index.php file. Here is the script usage:

```
 http://your_endpoint_path/api?term=[&list=10][&lang=en][&class=Automobile|River|Mammal]
```

Mandatory parameters:
  - **term**: filter for auto complention. Search is enabled if you provide at least two chars. 

Optional parameters:

  - **list**: maximum number of items returned. Default 10, max 100, min 1
  - **lang**: preferred language using the two chars international coding standard. Default is en (means english).
  - **class**: the name of the dbpedia classification. This examples supports Automobile (default), River, Mammal

Example:

the resource:

`http://localhost:8080/demo/api?term=ri&list=3&lang=en&class=River` 

will return something like:

```json
[ "River Garavogue", "River Oykel", "River Afan" ]
```

[Try in demo site](http://autocomplete.linkeddata.center/api?term=am&list=3&lang=en).

## The client side html code
The Html source with all required javascript is contatined in [pub/index.html] file.

## Reuse this approach
Please note that you can extend this approach to query any data in billons of linked data sources
(public or private) in just three steps:

 1. add the required dataset to the abox list in your linkeddata.center endpoint;
 2. start a learn job; 
 3. create your domain specific api to allow your application to access data
 
 To improve performances you can add cache at server side script.

## Support
Where possible, I will try and provide support for this project, feel free to open an issue and I'll do my best to help.

## Credits
I have to thank a *lot* of awesome open source projects that were suggested by [BOTK](http://ontology.it/tools/botk) architecture:

 - BOTK  packages.
 - Rest by Alexandre Gomes Gaigalas
 - Mimeparse by Joe Gregorio
 - Guzzle] by Michael Dowling
 - EasyRDF by Nicholas J Humfrey
 - Composer by Nils Adermann, Jordi Boggiano
 
And, of course, PHP and JQuery community.

## License
This project is licensed under the MIT license, in LICENSE file.

Power by ![LinkedData.Center logo](http://linkeddata.center/resources/v4/logo/Logo-colori-trasp_oriz-640x220.png)
