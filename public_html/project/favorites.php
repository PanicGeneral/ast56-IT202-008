<form class="mb-4">

    <div class="row">

        <div class="col-md-4">

            <input
                type="text"
                name="text"
                class="form-control"
                placeholder="Search Title"
                value="<?php se($_GET, "text"); ?>">

        </div>

        <div class="col-md-3">

            <select name="type" class="form-select">

                <option value="">All Types</option>
                <option value="japan">Japan</option>
                <option value="korea">Korea</option>
                <option value="china">China</option>

            </select>

        </div>

        <div class="col-md-3">

            <select name="nsfw" class="form-select">

                <option value="">All</option>
                <option value="0">No</option>
                <option value="1">Yes</option>

            </select>

        </div>

        <div class="col-md-2 d-flex gap-2">

            <button
                type="submit"
                class="btn btn-primary">

                Filter

            </button>

            <a
                href="favorites.php"
                class="btn btn-secondary">

                Reset

            </a>

        </div>

    </div>

</form>