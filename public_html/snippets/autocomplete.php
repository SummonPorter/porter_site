
<script type="text/javascript">
$(function() {
    var special = {
    <?php
		foreach ($wildcard_services as $service) {
            if ($service['name'] == "Special Request") {
                printf(
                    'value: "%s",
                    label: "%s",
                    desc: "%s",
                    id: "%s",
                    icon: "%s",
                    url: "%s"',
                    $service['name'],
                    $service['name'],
                    $service['description'],
                    "",
                    makeUrl($service['webIcon']),
                    makeUrl('/order/'.nameToSlug($service['name']))
                );
                break;
            }
		}
    ?>

    };
    var services = <?php
        $data = [];
        foreach ($service_types as $type) {
            $data[] = [
                'value' => $type['name'],
                'label' => $type['name'],
                'desc' => $type['description'],
                'id' => $type['id'],
                'icon' => makeUrl($type['webIcon']),
                'url' => makeUrl(
                    '/order/%s/%s/',
                    nameToSlug($parent_categories[$type['group']['id']]['name']),
                    nameToSlug($type['name'])
                )
            ];
        }
        echo json_encode($data);
        ?>;
    var focusServices = <?php
        $data = [];
        $wanted = ['General Handyman', 'General Moving Help', 'Run Errands', 'Junk Removal', 'Ikea Shopping and Assembly', 'Full Home Cleaning'];
        while (count($wanted)) {
            foreach ($service_types as $type) {
                if ($type['name'] == $wanted[0]) {
                    $data[] = [
                        'value' => $type['name'],
                        'label' => $type['name'],
                        'desc' => $type['description'],
                        'id' => $type['id'],
                        'icon' => makeUrl($type['webIcon']),
                        'url' => makeUrl(
                            '/order/%s/%s/',
                            nameToSlug($parent_categories[$type['group']['id']]['name']),
                            nameToSlug($type['name'])
                        )
                    ];
                    break;
                }
            }
            array_shift($wanted);
        }

        echo json_encode($data);
    ?>;

    $("#tags").autocomplete({
        position: { my : "left+"+(rem()*1.675)+"px top", at: "left bottom" },
        source: function (request, result) {
            var data = [];
            var term = request.term;

            if (term.length == 0) {
                result(focusServices);
                return;
            }


            try {
                ga('send', {
                    hitType: 'event',
                    eventCategory: 'Search',
                    eventAction: 'typed',
                    eventLabel: term
                });
            } catch (e) {}

            var search = new RegExp(term, 'i');
            for (var i in services) {
                if (services[i].value.match(search) ||
                    services[i].desc.match(search) ||
                    services[i].label.match(search)) {
                        data.push(services[i]);
                }
            }

            if (data.length == 0) {
                data.push(special);
            }

            result(data);
        },
        minLength: 0,
        classes: {
            "ui-autocomplete": "autocomplete"
        },
        select: function (event, ui) {
            window.location.href = ui.item.url;
        }
    }).focus(function(){
        $(this).autocomplete('search')
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
        .append( "<div> <div class=\"searchicon\" style=\"background-image:url('" + item.icon + "');\"></div> " + item.label + "</div>" )
        .appendTo( ul );
    };
});
</script>
