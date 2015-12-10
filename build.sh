
#set -e  # break the shell if any error occurs.

# set vars
productionDir="Production"

rm -rf ${productionDir} && gulp release && cp *.php ${productionDir} && mkdir ${productionDir}/core && cp -rf core/* ${productionDir}/core