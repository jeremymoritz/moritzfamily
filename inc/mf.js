//	Initialize JQuery on window load
$(function() {
	//	Apply JQuery Rollover on all IMG tags within a container element
	$(".mouseoverAll img").each(function() {
		$(this).addClass("mouseover");
	});
	//	JQuery Rollover!
	$("img.mouseover").each(function() {	//	activate this on images with the class "mouseover"
		var thisImage = $(this);
		
		var oldSrc = thisImage.attr('src');	//	non-mouseover src
		var overSrc = getPath(this) + getBasicName(this) + "_over." + getExt(this);	// (e.g. "images/myimage_over.png")
		
		preload = new Image(1,1);	//	preload the overImage
		preload.src = overSrc;
		
		thisImage.hover(function() {
			overSrc = getPath(this) + getBasicName(this) + "_over." + getExt(this);	// must be declared again for buttons that change src from other functions
			thisImage.attr("src", overSrc);	//	change to overSrc when mousing over
		}, function() {
			thisImage.attr("src", thisImage.attr("src").replace("_over",""));	// change back to oldSrc when mouse leaves
		});
	});
	
	if($("#quotes").length) {
		$(".hider").each(function() {
			var thisImage = $(this);
			var id = thisImage.attr("id");
			var explodedArr = id.split('_');
			var classToHide = explodedArr[2];	//	get the last thing part of the id which is the class to hide
				
			thisImage.click(function() {	//	onClick, certain quotes are hidden or shown
				$("." + classToHide).toggle();	//	hide or show divs with that many stars in them.
				
				var newSrc = thisImage.attr("src");	//	soon to be set as the new src
							
				if(newSrc.indexOf("hide") != -1) {	//	change button to be a show or hide button
					newSrc = newSrc.replace('hide', 'show');
				} else {
					newSrc = newSrc.replace('show', 'hide');
				}
				
				thisImage.attr("src", newSrc);
			});
		});
	}
	
	// JQuery Rollover Slide Enlarge!
	$("img.enlarge").each(function() {	//	activate this on images with the class "enlarge"
		var thisImage = $(this);	//	regular size image
		
		if(getPath(this).indexOf('thumbs/') != -1) {	//	if this is a thumbnail, look in main folder for image
			var bigId = getBasicName(this)	//	id of new image is same as basename of image (just in different folder)
			var bigSrc = getPath(this).replace('thumbs/','') + bigId + "." + getExt(this);	//	src of big image is similar to regular
			//alert(bigSrc);
		} else {
			var bigId = getBasicName(this) + "_large"	//	id of new image is basename of image + "_large"
			var bigSrc = getPath(this) + bigId + "." + getExt(this);	//	src of big image is similar to regular
		}
		$(document.body).append("<div class='overlarge' id='" + bigId + "'><img src='" + bigSrc + "' alt=''></div>");	//	create a new div with the image in it and append it as a sibling
		var bigImage = $("#"+bigId);	//	we are creating a jQuery reference to the div element even though we don't know its id
		
		thisImage.hover(function() {
			var offset = thisImage.offset();	//	get the offset (location on screen) of the current image
			
			bigImage.css("top",offset.top + thisImage.height() + 10).css("left",offset.left).slideDown(300);	//	set position and slide big image down
			thisImage.fadeTo(600, 0.3);	//	fade small image down to 30% opacity
		}, function() {	//	on mouseout
			bigImage.slideUp(300, function() {
				bigImage.css("display","none");	//	make sure it's gone
				bigImage.stop(true);	//	stop animations (clear the queue = true)
			});
			
			thisImage.fadeTo(500, 1.0, function() {	//	fade small image back up to 100% opacity
				thisImage.stop(true);	//	stop animations (clear the queue = true)
			});
		});
	});
	
	//	JQuery Logo slide on from left!
	$("#index #logo").css({width:0,height:"133px"});	//	set initial width to 0
	$("#index #logo").animate({ width:"700px" }, 1500);
});

function getPath(theImage) {
	var endPos = theImage.src.lastIndexOf('/') + 1;	//	find the position of the last "/" mark
	var path = theImage.src.substring(0, endPos);	//	the extension of the image
	return path;
}

function getBasicName(theImage) {
	var endPos = theImage.src.lastIndexOf('.');	//	find ending position of the basic name of the image (e.g. before ".png")
	var startPos = theImage.src.lastIndexOf('/') + 1;	//	find start position of the basic name of the image (e.g. after "images/")
	var basicName = theImage.src.substring(startPos, endPos);	//	the basic name of the image 
	return basicName;
}

function getExt(theImage) {
	var startPos = theImage.src.lastIndexOf('.') + 1;	//	find the starting position of the extension (after last ".")
	var extension = theImage.src.substring(startPos);	//	the extension of the image
	return extension;
}
