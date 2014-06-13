Channels
================

Add Entry/Field resources to your October CMS installation

###About
This plugin allows users to create channel and entry data similar to the way Craft CMS and EECMS
manages their data.

###Installation
Clone into your plugins directory under mey/channels. Log out then back into the October backend.

###Use
In order to begin, click on the "Channels" tab in the backend. At this point feel
free to begin creating your fields and channels. When you create a field you can attach it to a channel
in the Channel management section. When your channel is finished you can create a new entry from the entry
section. In the first page you will need to fill out basic entry information and then select the channel
that the entry belongs to. When you click save you will be taken to the page where you can fill out the fields
for this particular entry.

###Outputting Data
Attach the page component to whatever page you want the data to appear on. There is no set {% component %} tag
to add, it needs to be done manually. Channel entries can be looped through and accessed using the standard twig
tags. The array of entries lives in the entries index of the channel. In the template you have access to all field
data you assigned to the entry plus all the standard entry data which all entries have(name, published_at etc).

    [channel]
    limit = "10"
    channelName = "my-channel"
    sortBy = "published_at"
    orderBy = "desc"
    ==
    {% for entry in channel.entries %}
        {{entry.name}} <a href="">{{entry.url}}</a>
    {% endfor %}
