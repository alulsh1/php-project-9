<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>���������� �������</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body class="min-vh-100 d-flex flex-column">
    <header class="flex-shrink-0">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark px-3">
            <a class="navbar-brand" href="/">���������� �������</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
    <a class="nav-link active" href="/">�������</a>
</li>

                    <li class="nav-item">
    <a class="nav-link" href="/urls">�����</a>
</li>

                </ul>
            </div>
        </nav>
    </header>

    


    <main class="flex-grow-1">
        <div class="container-lg mt-3">
                      <h1></h1>
                    <div class="row">
    <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
        <h1 class="display-3">���������� �������</h1>
        <p class="lead">��������� ���������� ����� �� SEO �����������</p>
        <form action="/urls" method="post" class="row" required>
            <div class="col-8">
            <input
            type="text"
            name="url[name]"
            value=""
            class="form-control form-control-lg"
            placeholder="https://www.example.com"
            >
                        </div>
            <div class="col-2">
                <input type="submit" class="btn btn-primary btn-lg ms-3 px-5 text-uppercase mx-3" value="���������">
            </div>

        </form>

    </div>
</div>
        </div>
    </main>
	<?= $this->fetch('includes/footer.phtml'); ?>
</body>
</html>

