from fastapi import FastAPI
import os
from fastapi.responses import JSONResponse, FileResponse
import uvicorn

app = FastAPI(docs_url="/apis/tomcat/docs")


@app.get("/apis/tomcat/list_projects")
def list_projects():
    # Your code to list projects goes here
    try:
        response = {}
        projects = os.listdir("/u/relis/public_html/workspace/dslforge_workspace")
        projects = [i for i in projects if not i.startswith(".")]  # Remove hidden files
        for project in projects:
            if os.path.isdir(
                f"/u/relis/public_html/workspace/dslforge_workspace/{project.strip()}"
            ) and os.path.exists(
                f"/u/relis/public_html/workspace/dslforge_workspace/{project.strip()}/src-gen"
            ):
                response[project.strip()] = [
                    i
                    for i in os.listdir(
                        f"/u/relis/public_html/workspace/dslforge_workspace/{project.strip()}/src-gen"
                    )
                    if i.endswith(".php")
                ]

        return JSONResponse(response)
    except Exception as e:
        return JSONResponse({"error": str(e)})


@app.get("/apis/tomcat/get_project_configuration")
def get_project_configuration(project_name: str, file_name: str):
    # Your code to get project configuration goes here
    try:
        return FileResponse(
            path=f"/u/relis/public_html/workspace/dslforge_workspace/{project_name}/src-gen/{file_name}",
            media_type="text/plain",
            filename=file_name,
        )
    except Exception as e:
        return JSONResponse({"error": str(e)})


@app.post("/apis/tomcat/save_project_configuration")
def save_project_configuration(project_name: str, file_name: str, content: str):
    # Your code to save project configuration goes here
    try:
        if not os.path.exists(
            f"/u/relis/public_html/workspace/dslforge_workspace/{project_name}/src-gen"
        ):
            os.makedirs(
                f"/u/relis/public_html/workspace/dslforge_workspace/{project_name}/src-gen"
            )
        with open(
            f"/u/relis/public_html/workspace/dslforge_workspace/{project_name}/src-gen/{file_name}",
            "w",
        ) as f:
            f.write(content)
        return JSONResponse({"status": "success"})
    except Exception as e:
        return JSONResponse({"error": str(e)})


if __name__ == "__main__":
    uvicorn.run(app, port=8181, host="0.0.0.0", log_level="info")
