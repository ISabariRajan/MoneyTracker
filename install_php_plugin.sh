cur_dir=$(pwd)
echo $cur_dir

plugin_dir=$cur_dir/php_plugins
mkdir $plugin_dir
cd $plugin_dir

# ReadBean Plugin (ORM)
tar_file=$plugin_dir/redbeanphp.tar.gz
sudo apt install signify-openbsd -y
curl -L https://redbeanphp.com/downloadredbeanversion.php?f=all-drivers | signify -Vz -p ./red.pub -t arc | tar xvzf -
url=http://www.redbeanphp.com/downloadredbean.php
wget $url --output-document="$tar_file"
tar xvf $tar_file
rm $tar_file
rm $plugin_dir/license.txt
# sha256sum $tar_file
# cat $tar_file | signify -Vz -p red.pub -t arc | tar xvzf -