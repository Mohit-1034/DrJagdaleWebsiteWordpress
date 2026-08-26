#!/usr/bin/env bash
#
# Rebuild images/icons/ from the supplied source set, recoloured to the skin.
#
# WHY THIS EXISTS
#
# The icon set the clinic supplied is line art drawn in a mid blue (#4B5FA5).
# The site now runs the Orto skin's sage green, and blue icons on a green site
# read as a mistake. Rather than ask an illustrator for a second set, the hue is
# shifted here.
#
# It is a script rather than something done by hand because it has to be
# repeatable: the transform was applied in place once and then needed redoing
# when the first hue figure turned out to be wrong, and a second in-place pass
# would have compounded the first. Rebuilding from the originals every time
# cannot compound.
#
# THE NUMBERS
#
#   hue 32        ImageMagick's -modulate hue is 100 = no change with each unit
#                 worth 1.8 degrees, NOT 3.6 as is often assumed. Blue #4B5FA5
#                 sits at about 227 degrees and the skin's sage at about 105,
#                 so the shift is -122 degrees: 100 - (122 / 1.8) = 32.
#   saturation 62 The source blue is more saturated than the sage it becomes.
#   brightness 74 Green is perceptually lighter than blue at the same value, so
#                 a pure hue rotation left the icons floating on the cream chips
#                 they sit on. This puts the weight back.
#
# USAGE
#   SRC=/path/to/original/icons ./tools/recolour-icons.sh
#
# The originals are the DrJagdale*.png files from the client's HomePageImages
# folder. They are NOT in this repo - it holds only the recoloured output - so
# point SRC at wherever that folder lives.

set -euo pipefail

SRC="${SRC:-$HOME/Downloads/HomePageImages}"
DEST="$(cd "$(dirname "$0")/.." && pwd)/images/icons"

MODULATE="${MODULATE:-74,62,32}"

# Destination name -> source file. The names describe what the icon shows, so a
# template asks for 'knee' rather than for 'DrJagdale07'.
MAP="
knee               DrJagdale07.png
shoulder           DrJagdale09.png
hand               DrJagdale10.png
hip                DrJagdale12.png
foot               DrJagdale11.png
elbow              DrJagdale13.png
back               DrJagdale3.png
spine              DrJagdale2.png
joint              DrJagdale6.png
joint-replacement  DrJagdale01.png
arthroscopy        DrJagdale05.png
fracture           DrJagdale04.png
xray               DrJagdale17.png
physiotherapy      DrJagdale02.png
spinal-therapy     DrJagdale14.png
massage            DrJagdale18.png
diagnostics        DrJagdale08.png
team               DrJagdale20.png
home-visit         DrJagdale19.png
care               DrJagdale06.png
parking            DrJagdale16.png
strength           DrJagdale15.png
slipped-disc       DrJagdale5.png
spine-pain         DrJagdale8.png
"

if [ ! -d "$SRC" ]; then
	echo "Source directory not found: $SRC" >&2
	echo "Set SRC to the folder holding the original DrJagdale*.png icons." >&2
	exit 1
fi

mkdir -p "$DEST"
made=0
missing=0

while read -r name file; do
	[ -z "$name" ] && continue

	if [ ! -f "$SRC/$file" ]; then
		echo "  missing source: $file (for $name.png)" >&2
		missing=$((missing + 1))
		continue
	fi

	magick "$SRC/$file" -modulate "$MODULATE" -strip "$DEST/$name.png"
	made=$((made + 1))
done <<< "$MAP"

echo "Rebuilt $made icons into $DEST (modulate $MODULATE)."
[ "$missing" -gt 0 ] && echo "$missing source files were missing - those icons are unchanged." >&2
exit 0
