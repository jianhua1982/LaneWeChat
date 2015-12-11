
#set -e  # break the shell if any error occurs.

# set vars
productionDir="Production"
myDate=`date +%Y%m%d%H%M%S`

rm -rf ${productionDir} && gulp release && cp *.php ${productionDir} && mkdir ${productionDir}/core && cp -rf core/* ${productionDir}/core && mv ${productionDir} ${productionDir}.${myDate} && echo ${productionDir}.${myDate}成功构建