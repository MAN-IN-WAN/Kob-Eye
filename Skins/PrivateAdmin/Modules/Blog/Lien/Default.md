[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
	[MODULE [!Query!]/List]
	[MODULE Blog/CategorieLien/List]
[ELSE]
	<h1 class="alert alert-danger">Navigation error</h1>
[/IF]
