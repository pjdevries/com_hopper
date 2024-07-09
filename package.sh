baseDir=$(pwd)
srcDir=$baseDir/src
packageDir=$baseDir/packages

exclude=".git .gitignore \*update*.xml"

cd $baseDir
name=hopper
componentName=com_$name
version=$(grep '<version>' $srcDir/administrator/components/$componentName/$name.xml | sed -r 's#^\s*<version>(.*)</version>\s*$#\1#')
componentVersionName=$componentName-$version
versionDir=$packageDir/$componentVersionName
mkdir -p $versionDir

cd $srcDir
[ -f $versionDir/$componentVersionName.zip ] && rm $versionDir/$componentVersionName.zip
zip -r $versionDir/$componentVersionName.zip \
  administrator/components/$componentName \
  media/$componentName \
  --exclude $exclude administrator/components/$componentName/packages
cd  $srcDir/administrator/components/$componentName
zip $versionDir/$componentVersionName.zip $name.xml