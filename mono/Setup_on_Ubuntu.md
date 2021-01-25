# Set up your development environment
  On [Ubuntu](https://docs.monogame.net/articles/getting_started/1_setting_up_your_development_environment_ubuntu.html)
  
  - Check the [page](https://docs.monogame.net/articles/getting_started/1_setting_up_your_development_environment_ubuntu.html) where I got this from

## Install .NET Core SDK
Add repository:
```
wget https://packages.microsoft.com/config/ubuntu/20.04/packages-microsoft-prod.deb -O /tmp/packages-microsoft-prod.deb
sudo dpkg -i /tmp/packages-microsoft-prod.deb
sudo apt update
```
Install packages:

```
sudo apt-get install -y apt-transport-https
sudo apt-get install -y dotnet-sdk-3.1
```

## [Optional] Install mono
Mono is a C# runtime, just like .NET Core. If you're targeting Linux only, it's unnecessary, but if you're targeting some other platforms like Android, it's required.

Add repository:

```
sudo apt install gnupg ca-certificates
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys 3FA7E0328081BFF6A14DA29AA6A19B38D3D831EF
echo "deb https://download.mono-project.com/repo/ubuntu stable-focal main" | sudo tee /etc/apt/sources.list.d/mono-official-stable.list
sudo apt update
```
Install packages:

```
sudo apt install -y mono-devel
```

## Install Visual Studio Code

Add repository:
```
curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > /tmp/packages.microsoft.gpg
sudo install -o root -g root -m 644 /tmp/packages.microsoft.gpg /etc/apt/trusted.gpg.d/
sudo sh -c 'echo "deb [arch=amd64 signed-by=/etc/apt/trusted.gpg.d/packages.microsoft.gpg] https://packages.microsoft.com/repos/vscode stable main" > /etc/apt/sources.list.d/vscode.list'
sudo apt update
```
Install packages:
```
sudo apt-get install code
```
Install C# extension:

```
code --install-extension ms-dotnettools.csharp
```

