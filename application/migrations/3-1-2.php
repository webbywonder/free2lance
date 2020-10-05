<?php 
$modules = Module::all();
            foreach ($modules as $module) {
                  switch($module->name){
                    case "Dashboard":
                        $icon = "icon dripicons-meter";
                    break;
                    case "Messages":
                        $icon = "icon dripicons-message";
                    break;
                    case "Projects":
                        $icon = "icon dripicons-rocket";
                    break;
                    case "Clients":
                        $icon = "icon dripicons-user";
                    break;
                    case "Invoices":
                        $icon = "icon dripicons-document";
                    break;
                    case "Estimates":
                        $icon = "icon dripicons-document-edit";
                    break;
                    case "Expenses":
                        $icon = "icon dripicons-cart";
                    break;
                    case "Subscriptions":
                        $icon = "icon dripicons-retweet";
                    break;
                    case "Items":
                        $icon = "icon dripicons-shopping-bag";
                    break;
                    case "Quotations":
                        $icon = "icon dripicons-blog";
                    break;
                    case "Calendar":
                        $icon = "icon dripicons-calendar";
                    break;
                    case "Reports":
                        $icon = "icon dripicons-graph-pie";
                    break;
                    case "Tickets":
                        $icon = "icon dripicons-ticket";
                    break;
                    case "Settings":
                        $icon = "icon dripicons-toggles";
                    break;
                    default:
                    $icon = $module->icon;
                    break;
               }
               $module->icon = $icon;
               $module->save();
            }