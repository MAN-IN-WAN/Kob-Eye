angular.module("abt.Timeline", []);

angular.module("abt.Timeline").directive('abtTimeline',function(){

    var isVm = function(item){
            if( item['VmJobVmJobId'] > 0 || item['Titre'].indexOf('VMJOB') != -1 )
                return true;

            return false;
    }
    var isSamba = function(item){
        if( item['SambaJobSambaJobId'] > 0 || item['Titre'].indexOf('SAMBAJOB') != -1 )
            return true;

        return false;
    }
    var isRemote = function(item){
            if( item['RemoteJobRemoteJobId'] > 0 || item['Titre'].indexOf('REMOTEJOB') != -1 )
                return true;

            return item['RemoteJobRemoteJobId'] > 0;
    }



    return {
        restrict : 'A',
        template: function(element,attrs){
            return '<section id="cd-timeline" class="cd-container">\n' +
        '    <div class="cd-timeline-block" ng-repeat="item in itemList">\n' +
        '        <div class="cd-timeline-img cd-picture {{ item.Mode }} {{ item.Icon }}">\n' +
        '        </div> <!-- cd-timeline-img -->\n' +
        '\n' +
        '        <div class="cd-timeline-content">\n' +
        '            <h2 ng-bind-html="item.Title"></h2>\n' +
        '            <uib-progress ng-if="item.Progression < 100 && item.Type==\'Exec\'">\n' +
        '               <uib-bar value="item.Progression" type="warning" class=" progress-bar-striped progress-bar-animated"><span ng-hide="item.Progression < 5">{{ item.Progression }}%</span></uib-bar>\n' +
        '            </uib-progress>\n' +
        '            <p ng-bind-html="item.Content"></p>\n' +
        '            <a href="item.Url" class="cd-read-more" ng-if="item.Url">\n' +
        '                <span ng-if="!item.Link">En savoir plus</span>\n' +
        '                <span ng-if="item.Link" ng-bind-html="item.Link"></span>\n' +
        '            </a>\n' +
        '            <span class="cd-date" ng-bind-html="item.Date"></span>\n' +
        '        </div> <!-- cd-timeline-content -->\n' +
        '    </div> <!-- cd-timeline-block -->\n' +
        '</section> <!-- cd-timeline -->'},
        controller: function(){

        },
        link: function(scope, element, attrs){
            var context = attrs.abtTlContext;
            var filter = attrs.abtTlFilter;
            var store = scope[attrs.abtTimeline];

            store.setFilters(filter,context);
            store.getData(1,context).then(function(){
                scope.itemList = store.data[context];
                // angular.forEach(scope.itemList,function(v,k){
                //     v.Date = v.create;
                //     v.Title = v.Titre;
                //     v.Content = v.Details || 'Aucun Détail';
                // });

                scope.$watchCollection('itemList',function(){
                    angular.forEach(scope.itemList,function(v,k){
                        v.Date = v.create;
                        v.Title = v.Titre;
                        v.Content = v.Details || 'Aucun Détail';

                        switch (v.Type){
                            case 'Info':
                                if(v.Errors) {
                                    v.Mode = 'info_error';
                                } else{
                                    v.Mode = 'info';
                                }

                                break;
                            case 'Exec' :
                                if(v.Success){
                                    v.Mode = 'success';
                                }else if(v.Errors) {
                                    v.Mode = 'error';
                                }else if (v.Started){
                                    v.Mode = 'running';
                                }else{
                                    v.Mode = 'stopped';
                                }

                                break;
                            default :
                                v.Mode = 'default';
                        }

                        v.Icon = 'default';
                        if(isVm(v)){
                            v.Icon = 'Vm';
                        }
                        if(isSamba(v)){
                            v.Icon = 'Samba';
                        }
                        if(isRemote(v)){
                            v.Icon = 'Remote';
                        }

                    });
                });
            });
        }
    };
});