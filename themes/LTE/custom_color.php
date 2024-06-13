<?php 

$color = $lte_options['custom_color'];
?>
<style> 
	/*body , .container-fluid .right:hover, #nav ul li a.active, #nav ul li:hover a, .tableheader, #CC .header, #CC .current, #CC .weekday:hover, #CC .weekend:hover, #CC .current:hover{
	background: #f1f1f1;
}*/

.container-fluid .right:hover, #nav ul li a.active, #nav ul li:hover a, .tableheader, #CC .header, #CC .current, #CC .weekday:hover, #CC .weekend:hover, #CC .current:hover, .titletext, .navbar, .bottomBar2, .bottomBar {
	background: <?php echo $color; ?>;
}


.navbar-default h5{/*	background: #2494f2;*/    padding: 8px;   color : <?php echo $color; ?>; /*margin: 0; */   }
.cbp-hrmenu > ul > li.cbp-hropen > a:hover {
    color : <?php echo $color; ?>;
    
}

.navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav > .open > a:hover, .navbar-default .navbar-nav > .open > a:focus {
    background-color: <?php echo $color; ?>;

}

.navbar-default .navbar-nav > .open > a, .navbar-default .navbar-nav a:hover, .navbar-nav > .open > a:focus {
    background-color: <?php echo $color; ?>;

}

.cbp-hrmenu > ul > li > a:hover {
color:<?php echo $color; ?>;
}

table.tablestyle td.tableheader {
    background-color: <?php echo $color; ?> !important;
}
/*-----------*/

#nav ul li a:hover,#nav ul li a.active{
	border-left: 5px solid <?php echo $color; ?>;
}

html[dir="rtl"] #nav ul li a:hover, html[dir="rtl"] #nav ul li a.active{
	border-right: 5px solid <?php echo $color; ?>;
	border-left: 0px;
}
 #CC .header, #CC .header a,  #CC .current, #CC .weekday:hover, #CC .weekend:hover, #CC .current:hover, #CC .title , #CC .previous a, #CC .next a{
	 color: #000;
 }
#nav ul li a.active, #nav ul li:hover a { color : <?php echo $color; ?>;  }
/*.wrapper{ box-shadow: 0px 0px 20px #abadad; } */
.card-header, .sidebar-collapse nav#nav ul li:hover span {  background: <?php echo $color; ?>; } 

::-webkit-scrollbar-track {
    background: <?php echo $color; ?>;
    border: 4px solid transparent;
    background-clip: content-box;   /* this is important*/
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: <?php echo $color; ?>;
    border: 1px solid <?php echo $color; ?>;
}
a:hover, a:focus {
	color: <?php echo $color; ?>;
}
.sidebar .logo a {
	color: #0683bb;
}
table.tablestyle td.tableheader{
	color: #6b6565;
	background-color: #f3f3f3 !important
}

.subHeaders {	background: <?php echo $color; ?>; color :#fff; } 
button { background-color: <?php echo $color; ?>; }
.buttonui { color: #ecf0f1; background: <?php echo $color; ?>; }
select option:hover { background-color: <?php echo $color; ?>; }



.main-sidebar {
    background-color: <?php echo $color; ?>; }

    a:hover, a:focus {
    color: #ffffff;
}
</style>