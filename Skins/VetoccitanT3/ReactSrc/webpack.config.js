const path = require('path');

module.exports = {
   entry: './main.js',
   output: {
      path: path.join(__dirname, '/bundle'),
      filename: 'App.js'
   },
   module: {
      rules: [
        {
            test: /\.css$/i,
            use: ["style-loader", "css-loader"],
     	},
	{
            test: /\.jsx?$/,
            // exclude: /node_modules/,
            use: ['babel-loader']
	},
	{
            test: /\.svg$/,
            use: ['@svgr/webpack']
        }

      ]
   }
}
