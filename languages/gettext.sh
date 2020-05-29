# Change every instance of pmpro-addon-slug below to match your actual plugin slug
#---------------------------
# This script generates a new pmpro.pot file for use in translations.
# To generate a new pmpro-addon-slug.pot, cd to the main /pmpro-addon-slug/ directory,
# then execute `languages/gettext.sh` from the command line.
# then fix the header info (helps to have the old .pot open before running script above)
# then execute `cp languages/pmpro-member-homepages.pot languages/pmpro-member-homepages.po` to copy the .pot to .po
# then execute `msgfmt languages/pmpro-member-homepages.po --output-file languages/pmpro-member-homepages.mo` to generate the .mo
#---------------------------
echo "Updating pmpro-member-homepages.pot... "
xgettext -j -o languages/pmpro-member-homepages.pot \
--default-domain=pmpro-member-homepages \
--language=PHP \
--keyword=_ \
--keyword=__ \
--keyword=_e \
--keyword=_ex \
--keyword=_n \
--keyword=_x \
--sort-by-file \
--package-version=1.0 \
--msgid-bugs-address="info@paidmembershipspro.com" \
$(find . -name "*.php")
echo "Done!"