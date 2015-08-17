var git = angular.module('git', []);

git.controller('search', function($scope, $http) {
	$scope.branch = '';
	$scope.contributors = [];
	$scope.commits = [];

	$scope.filterState = {
		contributor: '',
		query: '',
		merge: false
	};

	$scope.drill = function(item) {
		var contributor = $scope.filterState.contributor;
		if(contributor != '' && contributor != item.author)
			return false;

		if(!$scope.filterState.merge && item.merge != '')
			return false;

		var query = $scope.filterState.query;
		if(query != '') {
			var words = query.toLowerCase().split(/\s+/g);
			var msg = item.msg.toLowerCase();
			for(var i = 0; i != words.length; ++i) {
				if(!msg.match(words[i])) {
					return false;
				}
			}
		}

		return true;
	};

	$http.get('/api/branch').
		then(function(response) {
			$scope.branch = response.data;
		});

	$http.get('/api/contributors').
		then(function(response) {
			$scope.contributors = response.data;
		});

	$http.get('/api/commits').
		then(function(response) {
			$scope.commits = response.data;
		});
	
	$('#author').on('changed.fu.selectlist', function () {
		$scope.filterState.contributor = $(this).find('input').val();
		$scope.$apply();
	});
});